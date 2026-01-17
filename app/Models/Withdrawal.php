<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $table = 'withdrawals';
    protected $fillable = [
        'mentor_id',
        'amount',
        'account_name',
        'account_number',
        'bank_name',
        'status',
        'processed_at',
        'completed_at',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
