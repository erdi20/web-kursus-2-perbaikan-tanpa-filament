<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $table = 'quiz_answers';

    protected $fillable = [
        'quiz_submission_id',
        'quiz_question_id',
        'selected_option',
        'is_correct',
    ];

    // Jawaban ini bagian dari submission tertentu
    public function submission(): BelongsTo
    {
        return $this->belongsTo(QuizSubmission::class, 'quiz_submission_id');
    }

    // Jawaban ini menjawab soal tertentu
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}
