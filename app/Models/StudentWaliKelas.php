<?php

namespace App\Models;

use Database\Factories\StudentWaliKelasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'class_id',
    'nis',
    'nisn',
    'name',
    'gender',
    'birth_place',
    'birth_date',
    'religion',
    'family_status',
    'child_order',
    'address',
    'previous_school',
    'registration_status',
    'accepted_class',
    'accepted_date',
    'father_name',
    'father_job',
    'mother_name',
    'mother_job',
    'parent_address',
    'parent_phone',
    'guardian_name',
    'guardian_job',
    'guardian_address',
    'guardian_phone',
    'status',
])]
class StudentWaliKelas extends Model
{
    /** @use HasFactory<StudentWaliKelasFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_wali_kelas';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'accepted_date' => 'date',
        ];
    }

    /**
     * Get the class (wali kelas) that owns the student.
     */
    public function classWaliKelas(): BelongsTo
    {
        return $this->belongsTo(ClassWaliKelas::class, 'class_id');
    }
}
