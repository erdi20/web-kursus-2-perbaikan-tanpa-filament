<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'class_material_id',
        'student_id',
        'photo_path',
        'attended_at',
    ];

    public function classMaterial()
    {
        return $this->belongsTo(ClassMaterial::class, 'class_material_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    protected $casts = [
        'attended_at' => 'datetime',
    ];
}
