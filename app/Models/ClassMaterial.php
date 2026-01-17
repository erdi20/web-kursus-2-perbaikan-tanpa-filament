<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassMaterial extends Model
{
    protected $table = 'class_materials';

    protected $fillable = [
        'course_class_id',
        'material_id',
        'order',
        'schedule_date',
        'visibility',
    ];
public function courseClass()
{
    return $this->belongsTo(\App\Models\CourseClass::class, 'course_class_id');
}
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_material_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    protected $casts = [
        'schedule_date' => 'datetime',
    ];
}
