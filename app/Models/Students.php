<?php

namespace App\Models;

use Database\Factories\StudentsFactory;
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
    'address',
    'parent_name',
    'parent_phone',
    'status',
])]
class Students extends Model
{
    /** @use HasFactory<StudentsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Get the class that owns the student.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
