<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\ClassMaterial;
use App\Models\QuizAnswer;
use App\Models\QuizAssignment;
use App\Models\QuizQuestion;
use App\Models\QuizSubmission;
use App\Services\MaterialCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function show(string $classId, string $assignmentId)
    {
        $user = Auth::user();

        //  Ambil assignment tanpa course_class_id
        $assignment = QuizAssignment::findOrFail($assignmentId);

        //  Validasi: pastikan material tugas ini ada di kelas ini
        $materialId = $assignment->material_id;
        $isValid = ClassMaterial::where('course_class_id', $classId)
            ->where('material_id', $materialId)
            ->exists();

        if (!$isValid) {
            abort(403, 'Kuis ini tidak tersedia untuk kelas ini.');
        }

        $questions = $assignment->questions;
        $submission = QuizSubmission::where('quiz_assignment_id', $assignmentId)
            ->where('student_id', $user->id)
            ->first();

        return view('student.quiz.quiz', compact('assignment', 'questions', 'submission', 'classId'));
    }

    public function submit(Request $request, string $classId, string $assignmentId)
    {
        $user = Auth::user();

        //  Validasi assignment dan konteks kelas
        $assignment = QuizAssignment::findOrFail($assignmentId);
        $materialId = $assignment->material_id;
        $isValid = ClassMaterial::where('course_class_id', $classId)
            ->where('material_id', $materialId)
            ->exists();

        if (!$isValid) {
            abort(403, 'Kuis tidak valid untuk kelas ini.');
        }

        // Ambil soal
        $questions = QuizQuestion::where('quiz_assignment_id', $assignmentId)->get();

        // Validasi jawaban
        $answers = [];
        $totalScore = 0;

        foreach ($questions as $question) {
            $answerKey = 'question_' . $question->id;
            $selected = $request->input($answerKey);

            if ($selected === null) {
                return back()->withErrors(['question_' . $question->id => 'Harap jawab semua soal.']);
            }

            $isCorrect = $selected === $question->correct_option;
            $score = $isCorrect ? $question->points : 0;

            $answers[] = [
                'quiz_question_id' => $question->id,
                'selected_option' => $selected,
                'is_correct' => $isCorrect,
            ];

            $totalScore += $score;
        }

        // Simpan submission
        $submission = QuizSubmission::updateOrCreate(
            [
                'quiz_assignment_id' => $assignmentId,
                'student_id' => $user->id,
            ],
            [
                'started_at' => now(),
                'submitted_at' => now(),
                'is_graded' => true,
                'score' => $totalScore,
            ]
        );

        // Simpan jawaban
        foreach ($answers as $ans) {
            $ans['quiz_submission_id'] = $submission->id;
            QuizAnswer::updateOrCreate(
                [
                    'quiz_submission_id' => $submission->id,
                    'quiz_question_id' => $ans['quiz_question_id'],
                ],
                $ans
            );
        }

        // ✅ Gunakan $classId langsung (bukan dari assignment)
        $enrollment = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', Auth::id())
            ->first();

        if ($enrollment) {
            $enrollment->updateProgress();
            app(\App\Services\GradingService::class)->updateEnrollmentGrade($enrollment);
        }
        // -----------------------------------
        app(MaterialCompletionService::class)
            ->checkAndMarkAsCompleted(Auth::id(), $classId, $assignment->material_id);
        return redirect()->route('quiz.result', [
            'classId' => $classId,
            'assignmentId' => $assignmentId
        ])->with('success', 'Quiz berhasil dikirim!');
    }


    public function result(string $classId, string $assignmentId)
    {
        $user = Auth::user();

        $assignment = QuizAssignment::with('questions')  // ✅ ini sudah cukup jika kolom benar
            ->findOrFail($assignmentId);

        $submission = QuizSubmission::where('quiz_assignment_id', $assignmentId)
            ->where('student_id', $user->id)
            ->firstOrFail();

        $materialId = $assignment->material_id;

        // ✅ Pastikan relasi 'question' dimuat
        $answers = QuizAnswer::with('question')
            ->where('quiz_submission_id', $submission->id)
            ->get();

        return view('student.quiz.hasilquiz', compact('assignment', 'submission', 'answers', 'classId', 'materialId'));
    }
}
