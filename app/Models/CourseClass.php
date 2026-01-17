<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseClass extends Model
{
    protected $table = 'course_classes';

    protected $fillable = [
        'name',
        'description',
        'course_id',
        'created_by',
        'status',
        'max_quota',
        'enrollment_start',
        'enrollment_end',
        'thumbnail'
    ];

    protected $casts = [
        'enrollment_start' => 'datetime',
        'enrollment_end' => 'datetime',
    ];

    // public function Course(): BelongsTo
    // {
    //     return $this->belongsTo(Course::class, 'course_id');
    // }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function CreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }

    public function courseClasses()
    {
        return $this
            ->belongsToMany(CourseClass::class, 'class_materials')
            ->withPivot('order', 'schedule_date', 'visibility');
    }

    public function materials(): BelongsToMany
    {
        return $this
            ->belongsToMany(Material::class, 'class_materials', 'course_class_id', 'material_id')
            ->withPivot(['order', 'schedule_date', 'visibility'])  // Kolom tambahan dari pivot
            ->withTimestamps()  // Jika kamu menggunakan created_at/updated_at di pivot
            ->orderBy('class_materials.order');  // <-- INI YANG DITAMBAHKAN
    }

    public function essayAssignments(): HasMany
    {
        return $this->hasMany(EssayAssignment::class);
    }

    // public function quizAssignments(): HasMany
    // {
    //     return $this->hasMany(QuizAssignment::class);
    // }
    // -------------------------
    public function classMaterials()
    {
        return $this->hasMany(ClassMaterial::class)->orderBy('order');
    }

    public function materialsFE(): BelongsToMany
    {
        return $this
            ->belongsToMany(Material::class, 'class_materials')
            ->withPivot('id', 'order', 'schedule_date', 'visibility')
            ->orderBy('class_materials.order');
    }

    // Helper: total bobot

    public function getTotalWeightAttribute(): int
    {
        return $this->essay_weight + $this->quiz_weight + $this->attendance_weight;
    }

    // Validasi: apakah bobot valid?
    public function isValidWeight(): bool
    {
        return $this->getTotalWeightAttribute() === 100;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail && Storage::disk('public')->exists($this->thumbnail)) {
            return Storage::url($this->thumbnail);
        }
        return null;
    }

    protected static function booted()
    {
        static::deleted(function ($courseClass) {
            // Hapus file thumbnail fisik saat data record dihapus
            if ($courseClass->thumbnail && Storage::disk('public')->exists($courseClass->thumbnail)) {
                Storage::disk('public')->delete($courseClass->thumbnail);
            }
        });
    }
}
