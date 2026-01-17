<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsensiMentorController extends Controller
{
    public function deletePhoto($id)
    {
    $attendance = Attendance::findOrFail($id);

        // 1. Cek apakah ada file fotonya
        if ($attendance->photo_path) {
            // 2. Hapus file fisik dari folder storage
            if (Storage::disk('public')->exists($attendance->photo_path)) {
                Storage::disk('public')->delete($attendance->photo_path);
            }

            // 3. Update database: set photo_path jadi null
            $attendance->update([
                'photo_path' => null
            ]);
        }

        return back()->with('success', 'File foto berhasil dihapus, data absensi tetap aman.');
    }
}
