<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\EssayAssignment;
use App\Models\EssaySubmission;
use App\Models\Material;
use App\Models\QuizAssignment;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class MateriMentorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $course = Course::with('materials')->findOrFail($id);

        if ($course->created_by !== auth()->id()) {
            abort(403);
        }

        $materials = $course->materials()->oldest()->get();  // Urutkan dari materi pertama

        return view('mentor.materi.index', compact('course', 'materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        return view('mentor.materi.createmateri', compact('course'));
    }

    public function edit(Course $course, Material $material)
    {
        return view('mentor.materi.editmateri', compact('course', 'material'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $course_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link_video' => 'nullable|url',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'image' => 'nullable|image|max:2048',
            'attendance_start' => 'nullable|required_with:is_attendance_required|date',
            'attendance_end' => 'nullable|required_with:is_attendance_required|date|after:attendance_start',
        ]);

        // Logic ambil ID Video YouTube
        $videoId = null;
        if ($request->link_video) {
            preg_match('/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?v=|embed\/|v\/|shorts\/))([^\?&"\'>]+)/', $request->link_video, $matches);
            $videoId = $matches[1] ?? null;
        }

        // ⚠️ KONVERSI WAKTU DARI WIB KE UTC
        $attendanceStart = null;
        $attendanceEnd = null;

        if ($request->has('is_attendance_required') && $request->attendance_start && $request->attendance_end) {
            // Parse input sebagai WIB, lalu ubah ke UTC
            $attendanceStart = Carbon::parse($request->attendance_start, 'Asia/Jakarta')->setTimezone('UTC');
            $attendanceEnd = Carbon::parse($request->attendance_end, 'Asia/Jakarta')->setTimezone('UTC');
        }

        $data = [
            'name' => $request->name,
            'content' => $request->content,
            'link_video' => $videoId,
            'course_id' => $course_id,
            'created_by' => auth()->id(),
            'is_attendance_required' => $request->has('is_attendance_required'),
            'attendance_start' => $attendanceStart,
            'attendance_end' => $attendanceEnd,
        ];

        if ($request->hasFile('pdf')) {
            $data['pdf'] = $request->file('pdf')->store('materials/pdf', 'public');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('materials/images', 'public');
        }

        Material::create($data);

        // Cari bagian return di paling bawah fungsi store
        return redirect()
            ->route('mentor.kelolakursusmateri', $course_id)
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    public function update(Request $request, $course_id, $id)
    {
        $material = Material::findOrFail($id);

        if ($material->created_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'pdf' => 'nullable|mimes:pdf|max:10000',
            'image' => 'nullable|image|max:2048',
            'attendance_start' => 'nullable|required_with:is_attendance_required|date',
            'attendance_end' => 'nullable|required_with:is_attendance_required|date|after:attendance_start',
        ]);

        $videoId = $material->link_video;
        if ($request->link_video && $request->link_video !== $material->link_video) {
            preg_match('/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?v=|embed\/|v\/|shorts\/))([^\?&"\'>]+)/', $request->link_video, $matches);
            $videoId = $matches[1] ?? $material->link_video;
        }

        // ⚠️ KONVERSI WAKTU DARI WIB KE UTC
        $attendanceStart = null;
        $attendanceEnd = null;

        if ($request->has('is_attendance_required') && $request->attendance_start && $request->attendance_end) {
            $attendanceStart = Carbon::parse($request->attendance_start, 'Asia/Jakarta')->setTimezone('UTC');
            $attendanceEnd = Carbon::parse($request->attendance_end, 'Asia/Jakarta')->setTimezone('UTC');
        }

        $data = [
            'name' => $request->name,
            'content' => $request->content,
            'link_video' => $videoId,
            'is_attendance_required' => $request->has('is_attendance_required'),
            'attendance_start' => $attendanceStart,
            'attendance_end' => $attendanceEnd,
        ];

        // --- LOGIC FILE PDF  ---
        if ($request->hasFile('pdf')) {
            if ($material->pdf)
                Storage::disk('public')->delete($material->pdf);
            $data['pdf'] = $request->file('pdf')->store('materials/pdf', 'public');
        } elseif ($request->has('remove_pdf')) {
            if ($material->pdf)
                Storage::disk('public')->delete($material->pdf);
            $data['pdf'] = null;
        }

        // --- LOGIC FILE GAMBAR  ---
        if ($request->hasFile('image')) {
            if ($material->image)
                Storage::disk('public')->delete($material->image);
            $data['image'] = $request->file('image')->store('materials/images', 'public');
        } elseif ($request->has('remove_image')) {
            if ($material->image)
                Storage::disk('public')->delete($material->image);
            $data['image'] = null;
        }

        $material->update($data);

        // Cari bagian return di paling bawah fungsi update
        return redirect()
            ->route('mentor.kelolakursusmateri', $course_id)
            ->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy($course_id, $id)
    {
        $material = Material::findOrFail($id);

        if ($material->created_by !== auth()->id()) {
            abort(403);
        }

        // Hapus file fisik dari storage sebelum hapus record database
        if ($material->pdf)
            Storage::disk('public')->delete($material->pdf);
        if ($material->image)
            Storage::disk('public')->delete($material->image);

        $material->delete();

        return back()->with('success', 'Materi telah dihapus dari kurikulum.');
    }

    public function manageContent($material_id)
    {
        $material = Material::with(['essayAssignments', 'course', 'classMaterials.attendances.student'])->findOrFail($material_id);
        // Ambil course dari relasi
        $course = $material->course;

        return view('mentor.materi.manage_content', compact('material', 'course'));
    }
}
