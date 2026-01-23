<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassEnrollment;
use App\Models\ClassMaterial;
use App\Models\CourseClass;
use App\Models\EssayAssignment;
use App\Models\EssaySubmission;
use App\Models\QuizAssignment;
use App\Models\QuizSubmission;

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
        // 1. Ambil semua materi di kelas ini
        $classMaterials = ClassMaterial::where('course_class_id', $class->id)
            ->with('material')
            ->get();

        // 2. Cek apakah SEMUA materi sudah selesai
        foreach ($classMaterials as $cm) {
            $isCompleted = app(MaterialCompletionService::class)
                ->isMaterialCompleted($studentId, $cm->id);

            if (!$isCompleted) {
                return false;  // Ada materi yang belum selesai
            }
        }

        // 3. Ambil semua assignment
        $materialIds = $classMaterials->pluck('material_id')->toArray();
        $essayAssignments = EssayAssignment::whereIn('material_id', $materialIds)->get();
        $quizAssignments = QuizAssignment::whereIn('material_id', $materialIds)->get();

        // 4. Cek apakah SEMUA essay sudah dikumpulkan DAN DINILAI
        foreach ($essayAssignments as $essay) {
            $submission = EssaySubmission::where('student_id', $studentId)
                ->where('essay_assignment_id', $essay->id)
                ->first();

            if (!$submission || !$submission->is_graded) {
                return false;
            }
        }

        // 5. Cek apakah SEMUA quiz sudah dikumpulkan
        foreach ($quizAssignments as $quiz) {
            $submissionExists = QuizSubmission::where('student_id', $studentId)
                ->where('quiz_assignment_id', $quiz->id)
                ->exists();

            if (!$submissionExists) {
                return false;
            }
        }

        // 6. ✅ CEK ABSENSI: Hanya untuk materi yang is_attendance_required = true
        $attendanceMateri = ClassMaterial::where('course_class_id', $class->id)
            ->whereHas('material', fn($q) => $q->where('is_attendance_required', true))
            ->get();

        // Jika ada materi yang wajib absen, pastikan siswa hadir di SEMUA-nya
        foreach ($attendanceMateri as $materi) {
            $hasAttended = Attendance::where('student_id', $studentId)
                ->where('class_material_id', $materi->id)
                ->exists();

            if (!$hasAttended) {
                return false;  // Belum hadir di salah satu sesi absen wajib
            }
        }

        return true;  // Semua syarat terpenuhi
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
