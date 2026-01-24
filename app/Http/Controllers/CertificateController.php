<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\CourseClass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function download(string $classId)
    {
        $enrollment = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        // Pastikan kode sertifikat ada
        if (empty($enrollment->certificate)) {
            $enrollment->update([
                'certificate' => 'CERT-' . strtoupper(Str::random(10)),
                'issued_at' => now(),
            ]);
        }

        // Ambil data situs dari database
        $siteSettings = \App\Models\Setting::first();
        $siteName = $siteSettings ? $siteSettings->site_name : 'Nama Website';

        // Load relasi: Enrollment -> Class -> Course -> User (Mentor)
        $class = CourseClass::with(['course.user'])->findOrFail($classId);
        $mentor = $class->course->user;  // Diambil dari created_by di tabel courses

        $pdf = Pdf::loadView('certificates.modern', [
            'student' => Auth::user(),
            'course' => $class->course,
            'enrollment' => $enrollment,
            'siteName' => $siteName,
            'mentor' => $mentor,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("Sertifikat_{$enrollment->certificate}.pdf");
    }
}
