<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KelasMentorController extends Controller
{
    public function index($id)
    {
        $course = Course::with('classes')->findOrFail($id);

        // Pastikan hanya pemilik yang bisa kelola
        if ($course->created_by !== auth()->id()) {
            abort(403);
        }

        $classes = $course->classes()->latest()->get();

        return view('mentor.kelas.index', compact('course', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $course_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_quota' => 'required|integer|min:1',
            'enrollment_start' => 'required|date',
            'enrollment_end' => 'required|date|after_or_equal:enrollment_start',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('class-thumbnails', 'public');
        }

        CourseClass::create([
            'name' => $request->name,
            'description' => $request->description,
            'course_id' => $course_id,
            'created_by' => auth()->id(),
            'status' => 'open',  // default langsung buka
            'max_quota' => $request->max_quota,
            'enrollment_start' => $request->enrollment_start,
            'enrollment_end' => $request->enrollment_end,
            'thumbnail' => $thumbnailPath,
        ]);

        return back()->with('success', 'Kelas baru berhasil dibuat!');
    }

    public function update(Request $request, $course_id, $id)
    {
        $class = CourseClass::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_quota' => 'required|integer|min:1',
            'enrollment_start' => 'required|date',
            'enrollment_end' => 'required|date|after_or_equal:enrollment_start',
            'status' => 'required|in:open,closed',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama
            if ($class->thumbnail && Storage::disk('public')->exists($class->thumbnail)) {
                Storage::disk('public')->delete($class->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('class-thumbnails', 'public');
        }

        $class->update($validated);

        return back()->with('success', 'Batch kelas berhasil diperbarui!');
    }

    public function destroy($course_id, $id)
    {
        $class = CourseClass::findOrFail($id);

        // Hapus file thumbnail
        if ($class->thumbnail && Storage::disk('public')->exists($class->thumbnail)) {
            Storage::disk('public')->delete($class->thumbnail);
        }

        $class->delete();

        return back()->with('success', 'Batch kelas berhasil dihapus!');
    }

    public function kelolaKelas($course_id, $class_id)
    {
        $course = Course::findOrFail($course_id);
        // Tambahkan eager loading untuk enrollments dan user
        $kelas = CourseClass::with(['materials', 'enrollments.user'])->findOrFail($class_id);

        $existingMaterialIds = $kelas->materials->pluck('id')->toArray();

        $availableMaterials = Material::where('course_id', $course_id)
            ->whereNotIn('id', $existingMaterialIds)
            ->get();

        $nextOrder = $kelas->materials->count() + 1;

        // Hitung jumlah terisi untuk header
        $currentEnrolled = $kelas->enrollments->count();

        return view('mentor.kelas.kelolakelas', compact(
            'course',
            'kelas',
            'availableMaterials',
            'nextOrder',
            'currentEnrolled'
        ));
    }

    public function syncMaterials(Request $request, $course_id, $class_id)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'order' => 'required|integer|min:1',
        ]);

        $kelas = CourseClass::findOrFail($class_id);

        // Ambil order terakhir + 1 jika tidak dikirim manual
        $order = $request->order;

        // Tambahkan materi ke pivot
        $kelas->materials()->attach($request->material_id, ['order' => $order]);

        return back()->with('success', 'Materi berhasil ditautkan ke kelas ini!');
    }

    // File: MentorController.php

    public function getEnrollmentDetail($id)
    {
        // Ambil data pendaftaran beserta siswanya
        $enrollment = \App\Models\ClassEnrollment::with(['user'])->findOrFail($id);

        $studentId = $enrollment->student_id;
        $classId = $enrollment->class_id;

        // 1. Hitung Materi (ClassMaterial)
        $classMaterials = \App\Models\ClassMaterial::where('course_class_id', $classId)->get();
        $totalMaterials = $classMaterials->count();
        $completedMaterials = \App\Models\MaterialCompletion::where('student_id', $studentId)
            ->whereIn('class_material_id', $classMaterials->pluck('id'))
            ->count();

        // 2. Hitung Essay & Quiz
        $materialIds = $classMaterials->pluck('material_id');

        $totalEssays = \App\Models\EssayAssignment::whereIn('material_id', $materialIds)->count();
        $completedEssays = \App\Models\EssaySubmission::where('student_id', $studentId)
            ->whereIn('essay_assignment_id', \App\Models\EssayAssignment::whereIn('material_id', $materialIds)->pluck('id'))
            ->count();

        $totalQuizzes = \App\Models\QuizAssignment::whereIn('material_id', $materialIds)->count();
        $completedQuizzes = \App\Models\QuizSubmission::where('student_id', $studentId)
            ->whereIn('quiz_assignment_id', \App\Models\QuizAssignment::whereIn('material_id', $materialIds)->pluck('id'))
            ->count();

        return response()->json([
            'name' => $enrollment->user->name,
            'email' => $enrollment->user->email,
            'progress' => $enrollment->progress_percentage,
            'avatar_url' => $enrollment->user->avatar_url,
            'status' => $enrollment->status,
            'activities' => [
                'materials' => [
                    'completed' => $completedMaterials,
                    'total' => $totalMaterials
                ],
                'essays' => [
                    'completed' => $completedEssays,
                    'total' => $totalEssays
                ],
                'quizzes' => [
                    'completed' => $completedQuizzes,
                    'total' => $totalQuizzes
                ]
            ]
        ]);
    }

    public function detachMaterial($course_id, $class_id, $material_id)
    {
        $kelas = CourseClass::findOrFail($class_id);
        $kelas->materials()->detach($material_id);
        return back()->with('success', 'Materi berhasil dihapus dari kelas.');
    }
}
