<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EssayAssignment extends Model
{
    protected $table = 'essay_assignments';

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'is_published',
        'allow_file_upload',
        'created_by',
        'material_id',
    ];

    // Relasi ke kelas
    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    // Relasi ke pembuat (dosen)
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke pengumpulan (submission)
    public function submissions(): HasMany
    {
        return $this->hasMany(EssaySubmission::class);
    }

    // app/Models/EssayAssignment.php

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
