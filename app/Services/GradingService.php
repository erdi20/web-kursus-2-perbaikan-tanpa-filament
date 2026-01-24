<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\CourseClass;
use App\Models\ClassMaterial;
use App\Models\QuizAssignment;
use App\Models\QuizSubmission;
use App\Models\ClassEnrollment;
use App\Models\EssayAssignment;
use App\Models\EssaySubmission;
use App\Models\MaterialCompletion;
use Illuminate\Support\Facades\Log;

class GradingService
{
    public function updateEnrollmentGrade(ClassEnrollment $enrollment): void
    {
        $class = $enrollment->courseClass;
        $studentId = $enrollment->student_id;

        // ✅ Cek dulu apakah siswa memenuhi syarat kelulusan
        $canComplete = $this->canBeMarkedAsCompleted($class, $studentId);

        if ($canComplete) {
            $result = $this->calculateFinalScore($class, $studentId);
            $isPassed = $result['final_score'] >= $class->min_final_score;
        } else {
            // Jika belum memenuhi syarat, jangan hitung nilai akhir
            $result = [
                'final_score' => $enrollment->grade ?? 0,
                'is_passed' => false,
            ];
            $isPassed = false;
        }

        $enrollment->update([
            'grade' => $result['final_score'],
            'status' => $isPassed ? 'completed' : 'active',
            'completed_at' => $isPassed ? now() : null,
        ]);
    }

    public function canBeMarkedAsCompleted(CourseClass $class, int $studentId): bool
    {
        // Ambil semua materi di kelas
        $classMaterials = ClassMaterial::where('course_class_id', $class->id)->get();

        foreach ($classMaterials as $cm) {
            // Cek apakah materi ini selesai
            if (!$this->isMaterialFullyCompleted($studentId, $cm->id)) {
                return false;
            }
        }

        return true;
    }

    private function isMaterialFullyCompleted(int $studentId, int $classMaterialId): bool
    {
        $classMaterial = ClassMaterial::with('material')->findOrFail($classMaterialId);
        $material = $classMaterial->material;

        // Jika tidak ada tugas, materi selesai saat diakses
        $hasTasks = EssayAssignment::where('material_id', $material->id)->exists() ||
            QuizAssignment::where('material_id', $material->id)->exists();

        if (!$hasTasks) {
            return MaterialCompletion::where('student_id', $studentId)
                ->where('class_material_id', $classMaterialId)
                ->exists();
        }

        // Cek essay
        $essayDone = EssayAssignment::where('material_id', $material->id)
            ->whereDoesntHave('submissions', function ($q) use ($studentId) {
                $q->where('student_id', $studentId)->where('is_graded', true);
            })
            ->count() == 0;

        // Cek quiz
        $quizDone = QuizAssignment::where('material_id', $material->id)
            ->whereDoesntHave('submissions', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->count() == 0;

        return $essayDone && $quizDone;
    }

    public function calculateFinalScore(CourseClass $class, int $studentId): array
    {
        // === 1. Ambil semua materi di kelas ini ===
        $materialIds = ClassMaterial::where('course_class_id', $class->id)
            ->pluck('material_id')
            ->toArray();

        if (empty($materialIds)) {
            return [
                'final_score' => 0,
                'is_passed' => false,
                'essay_avg' => 0,
                'quiz_avg' => 0,
                'attendance_percentage' => 0,
            ];
        }

        // === 2. Hitung nilai essay & quiz yang ADA ===
        $essayAssignments = EssayAssignment::whereIn('material_id', $materialIds)->get();
        $quizAssignments = QuizAssignment::whereIn('material_id', $materialIds)->get();

        $hasEssay = $essayAssignments->isNotEmpty();
        $hasQuiz = $quizAssignments->isNotEmpty();

        $essayAvg = 0;
        if ($hasEssay) {
            $essayScores = EssaySubmission::whereIn('essay_assignment_id', $essayAssignments->pluck('id'))
                ->where('student_id', $studentId)
                ->where('is_graded', true)
                ->pluck('score')
                ->all();

            $essayAvg = !empty($essayScores) ? array_sum($essayScores) / count($essayScores) : 0;
        }

        $quizAvg = 0;
        if ($hasQuiz) {
            $quizScores = QuizSubmission::whereIn('quiz_assignment_id', $quizAssignments->pluck('id'))
                ->where('student_id', $studentId)
                ->where('is_graded', true)
                ->pluck('score')
                ->all();

            $quizAvg = !empty($quizScores) ? array_sum($quizScores) / count($quizScores) : 0;
        }

        // === 3. Hitung kehadiran ===
        $attendanceAssignments = ClassMaterial::where('course_class_id', $class->id)
            ->whereHas('material', function ($q) {
                $q->where('is_attendance_required', true);
            })
            ->count();

        $attended = Attendance::whereHas('classMaterial', function ($q) use ($class) {
            $q
                ->where('course_class_id', $class->id)
                ->whereHas('material', function ($mq) {
                    $mq->where('is_attendance_required', true);
                });
        })
            ->where('student_id', $studentId)
            ->count();

        $attendancePercentage = $attendanceAssignments > 0 ? ($attended / $attendanceAssignments) * 100 : 0;
        $hasAttendance = $attendanceAssignments > 0;

        // === 4. Normalisasi Bobot ===
        $originalWeights = [
            'essay' => $hasEssay ? $class->course->essay_weight : 0,
            'quiz' => $hasQuiz ? $class->course->quiz_weight : 0,
            'attendance' => $hasAttendance ? $class->course->attendance_weight : 0,
        ];

        $totalWeight = array_sum($originalWeights);

        if ($totalWeight === 0) {
            // Jika tidak ada komponen penilaian sama sekali → lulus otomatis?
            $finalScore = 100;
        } else {
            // Normalisasi agar total = 100%
            $normalizedWeights = [
                'essay' => ($originalWeights['essay'] / $totalWeight) * 100,
                'quiz' => ($originalWeights['quiz'] / $totalWeight) * 100,
                'attendance' => ($originalWeights['attendance'] / $totalWeight) * 100,
            ];

            $finalScore = (
                ($essayAvg * $normalizedWeights['essay'])
                + ($quizAvg * $normalizedWeights['quiz'])
                + ($attendancePercentage * $normalizedWeights['attendance'])
            ) / 100;

            $finalScore = min(100, max(0, round($finalScore, 0)));
        }

        // === 5. Tentukan kelulusan ===
        $meetsAttendance = !$hasAttendance || $attendancePercentage >= $class->min_attendance_percentage;
        $meetsScore = $finalScore >= $class->min_final_score;
        $isPassed = $meetsAttendance && $meetsScore;

        return [
            'essay_avg' => $essayAvg,
            'quiz_avg' => $quizAvg,
            'attendance_percentage' => $attendancePercentage,
            'final_score' => $finalScore,
            'is_passed' => $isPassed,
        ];
    }

    public function updateEnrollmentScoreOnly(ClassEnrollment $enrollment): void
    {
        $class = $enrollment->courseClass;
        $studentId = $enrollment->student_id;

        $result = $this->calculateFinalScore($class, $studentId);

        // ✅ HANYA update nilai, TIDAK update status atau completed_at
        $enrollment->update([
            'grade' => $result['final_score'],
        ]);
    }
}
