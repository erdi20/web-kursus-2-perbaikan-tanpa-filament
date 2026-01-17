<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'course_id',
        'midtrans_order_id',
        'gross_amount',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'payment_payload',
        'settlement_at',
        'verified_at',
        'course_class_id',
        'student_id'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // RELASI BARU
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class, 'course_class_id');
    }
}
