<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuizQuestion extends Model
{
    protected $table = 'quiz_questions';

    protected $fillable = [
        'quiz_assignment_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'points',
    ];

    // Soal ini milik kuis tertentu
    public function quizAssignment(): BelongsTo
    {
        return $this->belongsTo(QuizAssignment::class);
    }

    // Satu soal bisa dijawab oleh banyak mahasiswa → banyak jawaban
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    protected $titleAttribute = 'clean_title';
}
