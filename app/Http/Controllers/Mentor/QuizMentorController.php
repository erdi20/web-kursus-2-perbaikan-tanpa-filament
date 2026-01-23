<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\QuizAssignment;
use App\Models\QuizQuestion;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizMentorController extends Controller
{
    public function storeQuiz(Request $request)
    {
        $request->validate([
            'material_id' => 'required',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|numeric|min:1',
            'due_date' => 'required|date',
        ]);

        QuizAssignment::create([
            'material_id' => $request->material_id,
            'title' => $request->title,
            'duration_minutes' => $request->duration_minutes,
            'due_date' => $request->due_date,
            'created_by' => Auth::id(),
            'is_published' => true,
        ]);

        return back()->with('success', 'Quiz berhasil dibuat! Sekarang tambahkan soal.');
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'quiz_assignment_id' => 'required|exists:quiz_assignments,id',
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_option' => 'required|in:a,b,c,d',
            'points' => 'required|numeric',
        ]);

        QuizQuestion::create($request->all());

        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function updateQuiz(Request $request, $id)
    {
        $quiz = QuizAssignment::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'due_date' => 'required|date',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'due_date' => \Carbon\Carbon::parse($request->due_date, 'Asia/Jakarta')->setTimezone('UTC'),
            'is_published' => $request->has('is_published'),
        ]);

        return back()->with('success', 'Pengaturan Quiz berhasil diperbarui!');
    }

    public function manageQuestions($id)
    {
        // Load quiz dengan relasi material.course agar bisa akses course
        $quiz = QuizAssignment::with(['material.course'])->findOrFail($id);

        $material = $quiz->material;
        $course = $material->course;  // ← ambil course dari relasi

        return view('mentor.materi.manage_questions', compact('quiz', 'material', 'course'));
    }

    public function updateQuestion(Request $request, $id)
    {
        $question = QuizQuestion::findOrFail($id);

        $request->validate([
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_option' => 'required|in:a,b,c,d',
            'points' => 'required|numeric',
        ]);

        $question->update($request->all());

        return back()->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroyQuestion($id)
    {
        $question = QuizQuestion::findOrFail($id);
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }

    public function destroy($id)
    {
        $quiz = QuizAssignment::findOrFail($id);

        // Hapus quiz (pastikan di database sudah set onDelete cascade untuk questions dan submissions)
        // Atau jika tidak pakai cascade, hapus manual relasinya di sini.
        $quiz->delete();
        return back()->with('success', 'Quiz berhasil dihapus dari materi!');
    }

    public function quizSubmissions($id)
    {
        // Load relasi lengkap sampai ke course
        $quiz = QuizAssignment::with([
            'material.classMaterials.courseClass.course'
        ])->findOrFail($id);

        $submissions = QuizSubmission::where('quiz_assignment_id', $id)
            ->with('student')
            ->latest()
            ->get();

        $course = $quiz->material->classMaterials->first()?->courseClass?->course;

        if (!$course) {
            abort(404, 'Kursus tidak ditemukan untuk kuis ini.');
        }

        $material_id = $quiz->material_id;

        return view('mentor.materi.quiz-submissions', compact('quiz', 'submissions', 'material_id', 'course'));
    }
}
