<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\ClassMaterial;
use App\Models\EssayAssignment;
use App\Models\EssaySubmission;
use App\Services\GradingService;
use Illuminate\Http\Request;

class EssayMentorController extends Controller
{
    public function storeEssay(Request $request, $material_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'due_date' => 'required|date',
        ]);

        EssayAssignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'material_id' => $material_id,
            'created_by' => auth()->id(),
            'is_published' => $request->has('is_published'),
        ]);

        return back()->with('success', 'Tugas Essay berhasil ditambahkan!');
    }

    // Update Essay

    public function updateEssay(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'due_date' => 'required|date',
        ]);

        $essay = EssayAssignment::findOrFail($id);
        $essay->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'is_published' => $request->has('is_published'),
        ]);

        return back()->with('success', 'Tugas Essay berhasil diperbarui!');
    }

    // Hapus Essay
    public function destroyEssay($id)
    {
        $essay = EssayAssignment::findOrFail($id);
        $essay->delete();

        return back()->with('success', 'Tugas Essay telah dihapus.');
    }

    public function submissions($essay_id)
    {
        $essay = EssayAssignment::with(['material.course'])->findOrFail($essay_id);

        // Ambil course dari relasi yang sudah di-load
        $course = $essay->material->course;

        $submissions = EssaySubmission::with(['student'])
            ->where('essay_assignment_id', $essay_id)
            ->latest('submitted_at')
            ->get();

        return view('mentor.materi.essay_submissions', compact('essay', 'submissions', 'course'));
    }

    public function gradeSubmission(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission = EssaySubmission::findOrFail($id);

        // 1. Update data submission
        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'is_graded' => true,
        ]);

        // 2. Cari keterkaitan dengan Enrollment (copy logika dari Filament lo)
        $material = $submission->assignment->material;
        $classMaterial = ClassMaterial::where('material_id', $material->id)->first();

        if ($classMaterial) {
            $enrollment = ClassEnrollment::where('student_id', $submission->student_id)
                ->where('class_id', $classMaterial->course_class_id)
                ->first();

            if ($enrollment) {
                // Update progress (centang materi selesai)
                $enrollment->updateProgress();

                app(GradingService::class)->updateEnrollmentGrade($enrollment);
            }
        }

        return back()->with('success', 'Nilai berhasil disimpan dan kalkulasi nilai kelas diperbarui!');
    }
}
