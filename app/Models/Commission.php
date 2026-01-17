<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commissions';

    protected $fillable = [
        'payment_id',
        'mentor_id',
        'amount',
        'percentage',
        'paid_at',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            Payment::class,
            'id',  // foreign key di payments
            'id',  // local key di courses
            'payment_id',  // local key di commissions
            'course_id'  // foreign key di payments
        );
    }
}
