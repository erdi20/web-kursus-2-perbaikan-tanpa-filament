<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $table = 'courses';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'thumbnail',
        'price',
        'status',
        'created_by',
        'discount_price',
        'discount_end_date',
        'essay_weight',
        'quiz_weight',
        'attendance_weight',
        'min_attendance_percentage',
        'min_final_score',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class, 'course_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'course_id');
    }

    // ---------
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    // app/Models/Course.php
    public function enrollments(): HasManyThrough
    {
        return $this->hasManyThrough(
            ClassEnrollment::class,
            CourseClass::class,
            'course_id',  // course_classes.course_id → courses.id
            'class_id',  // class_enrollments.class_id → course_classes.id
            'id',
            'id'
        )->whereNotNull('rating');  // ← tambahkan ini agar hanya hitung yang punya rating
    }

    public function classEnrollments(): HasMany
    {
        // Asumsi foreign key di tabel 'class_enrollments' adalah 'class_id'
        // dan merujuk ke 'id' dari 'course_classes'
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }

    protected static function booted()
    {
        static::deleted(function ($course) {
            // Hapus file thumbnail jika ada saat data kursus dihapus
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
        });
        static::creating(function ($course) {
            // Otomatis bikin slug saat data pertama kali dibuat
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->name);
            }
        });

        static::updating(function ($course) {
            // Opsional: Update slug kalau namanya berubah
            if ($course->isDirty('name')) {
                $course->slug = Str::slug($course->name);
            }
        });
    }
}
