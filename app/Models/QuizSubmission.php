<?php

namespace App\Models;

use App\Models\QuizAssignment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    protected $table = 'quiz_submissions';

    protected $fillable = [
        'quiz_assignment_id',
        'student_id',
        'started_at',
        'submitted_at',
        'is_graded',
        'score',
        'feedback',
    ];

    // Submission ini milik kuis tertentu
    public function quizAssignment(): BelongsTo
    {
        return $this->belongsTo(QuizAssignment::class);
    }

    // Submission ini dibuat oleh mahasiswa (user)
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Satu submission berisi banyak jawaban (per soal)
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(QuizAssignment::class, 'quiz_assignment_id');
    }

    // apakah sudah terlambat

    public function isLate(): bool
    {
        // Jika tidak ada assignment atau tidak ada due_date, anggap tidak terlambat
        if (!$this->quizAssignment || !$this->quizAssignment->due_date) {
            return false;
        }

        // Jika belum submit (seharusnya tidak terjadi, tapi antisipasi)
        if (!$this->submitted_at) {
            return false;
        }

        // Bandingkan submitted_at dengan due_date
        return $this->submitted_at > $this->quizAssignment->due_date;
    }
}
