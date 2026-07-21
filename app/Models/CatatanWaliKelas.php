<?php

namespace App\Models;

use Database\Factories\CatatanWaliKelasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'student_id',
    'catatan',
])]
class CatatanWaliKelas extends Model
{
    /** @use HasFactory<CatatanWaliKelasFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catatan_wali_kelas';

    /**
     * Get the student that owns the catatan wali kelas record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentWaliKelas::class, 'student_id');
    }
}
