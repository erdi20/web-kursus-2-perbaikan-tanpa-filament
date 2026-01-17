<?php

namespace App\Observers;

use App\Models\CourseClass;
use App\Services\GradingService;

class CourseClassObserver
{
    public function updated(CourseClass $class): void
    {
        // Hanya jalankan jika status berubah ke 'closed'
        if ($class->isDirty('status') && $class->status === 'closed') {
            foreach ($class->enrollments as $enrollment) {
                // Update progres (jika belum)
                $enrollment->updateProgress();

                // Hitung ulang nilai & kelulusan
                app(GradingService::class)->updateEnrollmentGrade($enrollment);
            }
        }
    }
}
