<?php

// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassEnrollment;
use App\Models\ClassMaterial;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function store(Request $request, string $classId)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'material_id' => 'required|exists:materials,id',  // Pastikan ID materi dikirim
        ]);

        // 1. Cari record pivot yang menghubungkan kelas ini dan materi ini
        $pivot = ClassMaterial::with('material')
            ->where('course_class_id', $classId)
            ->where('material_id', $request->material_id)
            ->first();

        // 2. Validasi: Apakah materi ditemukan dan butuh absen?
        if (!$pivot || !$pivot->material->is_attendance_required) {
            return response()->json(['error' => 'Sesi absensi tidak ditemukan untuk materi ini.'], 404);
        }

        // 3. Validasi Waktu: Apakah saat ini masih dalam rentang waktu absen?
        $now = now();
        $start = $pivot->material->attendance_start;
        $end = $pivot->material->attendance_end;

        if (!$now->between($start, $end)) {
            return response()->json(['error' => 'Waktu absensi sudah berakhir atau belum dimulai.'], 403);
        }

        // 4. Cek apakah sudah pernah absen
        if (Attendance::where('class_material_id', $pivot->id)
                ->where('student_id', Auth::id())
                ->exists()) {
            return response()->json(['error' => 'Anda sudah melakukan absensi untuk materi ini.'], 400);
        }

        // 5. Simpan foto
        $photoPath = $request->file('photo')->store('attendances', 'public');

        // 6. Simpan absensi
        Attendance::create([
            'class_material_id' => $pivot->id,
            'student_id' => Auth::id(),
            'photo_path' => $photoPath,
            'attended_at' => now(),
        ]);

        // 7. Update progress
        $enrollment = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', Auth::id())
            ->first();

        if ($enrollment) {
            $enrollment->updateProgress();
            app(GradingService::class)->updateEnrollmentGrade($enrollment);
        }

        return response()->json(['success' => true, 'message' => 'Absensi berhasil!']);
    }
}
