<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'name',
        'content',
        'link_video',
        'image',
        'pdf',
        'course_id',
        'created_by',
        'is_attendance_required',
        'attendance_start',
        'attendance_end',
    ];

    public function Course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function CreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function essayAssignments()
    {
        return $this->hasMany(EssayAssignment::class, 'material_id');
    }

    public function classMaterials()
    {
        return $this->hasMany(ClassMaterial::class, 'material_id');
    }

    public function quizAssignments()
    {
        return $this->hasMany(QuizAssignment::class, 'material_id');
    }

    // Di dalam class Material
    public function attendances()
    {
        // Karena Attendance berelasi ke ClassMaterial, dan ClassMaterial ke Material
        return $this->hasManyThrough(
            Attendance::class,
            ClassMaterial::class,
            'material_id',  // Foreign key di ClassMaterial
            'class_material_id',  // Foreign key di Attendance
            'id',  // Local key di Material
            'id'  // Local key di ClassMaterial
        );
    }

    protected $casts = [
        'is_attendance_required' => 'boolean',
        'attendance_start' => 'datetime',
        'attendance_end' => 'datetime',
    ];
}
