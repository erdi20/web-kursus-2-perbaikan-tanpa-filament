<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\ClassMaterial;
use App\Models\EssayAssignment;
use App\Models\EssaySubmission;
use App\Services\GradingService;
use App\Services\MaterialCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EssayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('student.essay.index');
    }

    public function show(string $classId, string $assignmentId)
    {
        $user = Auth::user();

        // Validasi: user terdaftar di kelas?
        $enrollment = ClassEnrollment::where('student_id', $user->id)
            ->where('class_id', $classId)
            ->firstOrFail();

        $assignment = EssayAssignment::where('id', $assignmentId)
            ->where('is_published', true)
            ->firstOrFail();

        //  Ambil material_id dari assignment
        $materialId = $assignment->material_id;

        //  Validasi: materi ini ada di kelas ini
        $isMaterialInClass = ClassMaterial::where('course_class_id', $classId)
            ->where('material_id', $materialId)
            ->exists();
        if (!$isMaterialInClass) {
            abort(403);
        }

        $submission = EssaySubmission::where('essay_assignment_id', $assignmentId)
            ->where('student_id', $user->id)
            ->first();

        return view('student.essay.index', compact(
            'assignment', 'submission', 'classId', 'materialId'  // ← tambahkan ini
        ));
    }

    public function submit(Request $request, string $classId, string $assignmentId)
    {
        $request->validate([
            'essay_answer' => 'required|string|max:10000',
        ]);

        $user = Auth::user();

        //  Ambil assignment tanpa course_class_id
        $assignment = EssayAssignment::findOrFail($assignmentId);

        //  Validasi: pastikan tugas ini ada di kelas ini
        $materialId = $assignment->material_id;
        $isValid = ClassMaterial::where('course_class_id', $classId)
            ->where('material_id', $materialId)
            ->exists();

        if (!$isValid) {
            abort(403, 'Tugas tidak valid untuk kelas ini.');
        }

        // Simpan jawaban
        EssaySubmission::updateOrCreate(
            [
                'essay_assignment_id' => $assignmentId,
                'student_id' => $user->id,
            ],
            [
                'answer_text' => $request->essay_answer,
                'submitted_at' => now(),
                'is_graded' => false,
            ]
        );

        //  Gunakan $classId langsung (bukan dari assignment)
        $enrollment = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', Auth::id())
            ->first();

        if ($enrollment) {
            $enrollment->updateProgress();
            // app(GradingService::class)->updateEnrollmentGrade($enrollment);
        }
        // -----------------------------------------------------
        app(MaterialCompletionService::class)
            ->checkAndMarkAsCompleted(Auth::id(), $classId, $assignment->material_id);
        return redirect()->route('materials.show', [
            'classId' => $classId,
            'materialId' => $assignment->material_id
        ]);
    }
}
