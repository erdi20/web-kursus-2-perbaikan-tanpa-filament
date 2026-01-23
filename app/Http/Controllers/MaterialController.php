<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\CourseClass;
use App\Models\EssaySubmission;
use App\Models\Material;
use App\Models\QuizSubmission;
use App\Services\MaterialCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    public function show(string $classId, string $materialId)
    {
        $user = Auth::user();

        // 1. Validasi: user terdaftar di kelas
        $enrollment = ClassEnrollment::where('student_id', $user->id)
            ->where('class_id', $classId)
            ->firstOrFail();

        // 2. Load kelas dengan relasi yang dibutuhkan
        $class = CourseClass::with([
            'course',
            'classMaterials.material'
        ])->findOrFail($classId);

        // 3. Ambil materi dengan tugas
        $material = Material::with([
            'essayAssignments' => fn($q) => $q->where('is_published', true),
            'quizAssignments' => fn($q) => $q->where('is_published', true),
        ])->findOrFail($materialId);

        // 4. Validasi: apakah materi ini memang bagian dari kelas tersebut
        $isMaterialInClass = $class->materials()->where('materials.id', $materialId)->exists();
        if (!$isMaterialInClass) {
            abort(403, 'Materi tidak ditemukan di kelas ini.');
        }

        // --- LOGIKA PROGRESS & COMPLETION ---

        $completionService = app(\App\Services\MaterialCompletionService::class);

        // A. Tandai bahwa materi ini SUDAH DIBUKA (Mencegah bug progres langsung 100%)
        $completionService->markAsAccessed($user->id, $classId, $materialId);

        // B. Cek apakah tugas-tugas di dalamnya (essay/quiz) sudah lengkap
        $completionService->checkAndMarkAsCompleted($user->id, $classId, $materialId);

        // C. Update persentase progres di tabel enrollments
        $enrollment->updateProgress();

        // ------------------------------------

        // Ambil data submission untuk kebutuhan tampilan (UI)
        $userEssaySubmissions = EssaySubmission::where('student_id', $user->id)
            ->whereIn('essay_assignment_id', $material->essayAssignments->pluck('id'))
            ->pluck('essay_assignment_id')
            ->toArray();

        $userQuizSubmissions = QuizSubmission::where('student_id', $user->id)
            ->whereIn('quiz_assignment_id', $material->quizAssignments->pluck('id'))
            ->pluck('quiz_assignment_id')
            ->toArray();

        $userEssayDetails = EssaySubmission::where('student_id', $user->id)
            ->whereIn('essay_assignment_id', $material->essayAssignments->pluck('id'))
            ->with('assignment')
            ->get()
            ->keyBy('essay_assignment_id');

        // --- LOGIKA ABSENSI ---
        $isCurrentMaterialForAttendance = false;
        $hasAttended = false;
        $now = now();

        if ($material->is_attendance_required && $material->attendance_start && $material->attendance_end) {
            if ($now->between($material->attendance_start, $material->attendance_end)) {
                $isCurrentMaterialForAttendance = true;
                $pivot = \App\Models\ClassMaterial::where('course_class_id', $classId)
                    ->where('material_id', $materialId)
                    ->first();

                if ($pivot) {
                    $hasAttended = \App\Models\Attendance::where('class_material_id', $pivot->id)
                        ->where('student_id', $user->id)
                        ->exists();
                }
            }
        }

        return view('student.material.materi', compact(
            'class',
            'material',
            'enrollment',
            'userEssaySubmissions',
            'userQuizSubmissions',
            'isCurrentMaterialForAttendance',
            'hasAttended',
            'userEssayDetails'
        ));
    }

 
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
