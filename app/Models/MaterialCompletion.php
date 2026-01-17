<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialCompletion extends Model
{
    protected $table = 'material_completions';

    protected $fillable = [
        'student_id',
        'class_material_id',
        'completed_at',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classMaterial()
    {
        return $this->belongsTo(ClassMaterial::class, 'class_material_id');
    }
}
