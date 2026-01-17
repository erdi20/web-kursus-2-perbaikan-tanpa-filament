<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassEnrollment extends Model
{
    protected $table = 'class_enrollments';

    protected $fillable = [
        'class_id',
        'student_id',
        'enrolled_at',
        'progress_percentage',
        'completed_at',
        'status',
        'grade',
        'certificate',
        'issued_at',
        'is_verified',
        'review',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');  // foreign key = student_id
    }

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'issued_at' => 'datetime',
    ];

    // public function updateProgress(): void
    // {
    //     $classId = $this->class_id;
    //     $studentId = $this->student_id;

    //     // 1. Ambil semua material_id di kelas ini
    //     $materialIds = ClassMaterial::where('course_class_id', $classId)
    //         ->pluck('material_id')
    //         ->toArray();

    //     if (empty($materialIds)) {
    //         $this->update(['progress_percentage' => 0]);
    //         return;
    //     }

    //     // 2. Hitung total aktivitas yang ADA
    //     $totalEssays = EssayAssignment::whereIn('material_id', $materialIds)->count();
    //     $totalQuizzes = QuizAssignment::whereIn('material_id', $materialIds)->count();
    //     $totalAttendances = ClassMaterial::whereIn('material_id', $materialIds)
    //         ->whereHas('material', fn($q) => $q->where('is_attendance_required', true))
    //         ->count();

    //     $totalActivities = $totalEssays + $totalQuizzes + $totalAttendances;

    //     if ($totalActivities === 0) {
    //         $this->update(['progress_percentage' => 0]);
    //         return;
    //     }

    //     // 3. Hitung aktivitas yang sudah dilakukan
    //     $completedEssays = EssaySubmission::whereIn('essay_assignment_id',
    //         EssayAssignment::whereIn('material_id', $materialIds)->pluck('id'))->where('student_id', $studentId)->count();

    //     $completedQuizzes = QuizSubmission::whereIn('quiz_assignment_id',
    //         QuizAssignment::whereIn('material_id', $materialIds)->pluck('id'))->where('student_id', $studentId)->count();

    //     $completedAttendances = Attendance::where('student_id', $studentId)
    //         ->whereIn('class_material_id',
    //             ClassMaterial::whereIn('material_id', $materialIds)
    //                 ->whereHas('material', fn($q) => $q->where('is_attendance_required', true))
    //                 ->pluck('id'))
    //         ->count();

    //     $completed = $completedEssays + $completedQuizzes + $completedAttendances;

    //     // 4. Hitung progres
    //     $progress = min(100, round(($completed / $totalActivities) * 100, 2));

    //     $this->update(['progress_percentage' => $progress]);
    // }

    // Cek apakah materi selesai

    public function isMaterialCompleted(int $classMaterialId): bool
    {
        $completion = MaterialCompletion::where('student_id', $this->student_id)
            ->where('class_material_id', $classMaterialId)
            ->first();

        if ($completion && $completion->completed_at) {
            return true;
        }

        // Cek: apakah semua tugas sudah dikerjakan?
        $classMaterial = ClassMaterial::findOrFail($classMaterialId);
        $materialId = $classMaterial->material_id;

        $totalEssays = EssayAssignment::where('material_id', $materialId)->count();
        $totalQuizzes = QuizAssignment::where('material_id', $materialId)->count();

        if ($totalEssays === 0 && $totalQuizzes === 0) {
            // Jika tidak ada tugas, cukup akses = selesai
            return $this->hasAccessedMaterial($classMaterialId);
        }

        $completedEssays = EssaySubmission::where('student_id', $this->student_id)
            ->whereIn('essay_assignment_id', EssayAssignment::where('material_id', $materialId)->pluck('id'))
            ->count();

        $completedQuizzes = QuizSubmission::where('student_id', $this->student_id)
            ->whereIn('quiz_assignment_id', QuizAssignment::where('material_id', $materialId)->pluck('id'))
            ->count();

        return ($completedEssays >= $totalEssays) && ($completedQuizzes >= $totalQuizzes);
    }

    // Cek apakah pernah akses (opsional, bisa pakai log atau asumsi saat buka halaman)
    private function hasAccessedMaterial(int $classMaterialId): bool
    {
        // Anda bisa buat log saat buka materi, atau asumsikan "selesai" jika tidak ada tugas
        return true;  // atau implementasi log akses
    }

    public function updateProgress(): void
    {
        $classId = $this->class_id;
        $studentId = $this->student_id;

        // 1. Ambil semua ClassMaterial di kelas ini
        $classMaterials = ClassMaterial::where('course_class_id', $classId)->get();
        $classMaterialIds = $classMaterials->pluck('id')->toArray();
        $materialIds = $classMaterials->pluck('material_id')->toArray();

        if (empty($classMaterialIds)) {
            $this->update(['progress_percentage' => 0]);
            return;
        }

        // 2. Definisi Total Aktivitas:
        // Setiap Materi (Membaca) + Setiap Essay + Setiap Quiz + Setiap Absen
        $totalMaterials = count($classMaterialIds);
        $totalEssays = EssayAssignment::whereIn('material_id', $materialIds)->count();
        $totalQuizzes = QuizAssignment::whereIn('material_id', $materialIds)->count();
        $totalAttendances = $classMaterials->where('material.is_attendance_required', true)->count();

        $totalActivities = $totalMaterials + $totalEssays + $totalQuizzes + $totalAttendances;

        // 3. Hitung yang SUDAH Selesai
        // a. Materi yang sudah diakses/dibuka
        $completedMaterials = MaterialCompletion::where('student_id', $studentId)
            ->whereIn('class_material_id', $classMaterialIds)
            ->count();

        // b. Essay
        $completedEssays = EssaySubmission::where('student_id', $studentId)
            ->whereIn('essay_assignment_id', EssayAssignment::whereIn('material_id', $materialIds)->pluck('id'))
            ->count();

        // c. Quiz
        $completedQuizzes = QuizSubmission::where('student_id', $studentId)
            ->whereIn('quiz_assignment_id', QuizAssignment::whereIn('material_id', $materialIds)->pluck('id'))
            ->count();

        // d. Absen
        $completedAttendances = Attendance::where('student_id', $studentId)
            ->whereIn('class_material_id', $classMaterialIds)
            ->count();

        $completedCount = $completedMaterials + $completedEssays + $completedQuizzes + $completedAttendances;

        // 4. Hitung persentase secara akurat
        $progress = ($completedCount / $totalActivities) * 100;

        // Safety check agar tidak lebih dari 100
        $progress = min(100, round($progress, 2));

        $this->update(['progress_percentage' => $progress]);
    }
}
