<?php

namespace App\Models;

use Database\Factories\AbsensiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'student_id',
    'hadir',
    'izin',
    'sakit',
    'alpa',
])]
class Absensi extends Model
{
    /** @use HasFactory<AbsensiFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absensis';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hadir' => 'integer',
            'izin' => 'integer',
            'sakit' => 'integer',
            'alpa' => 'integer',
        ];
    }

    /**
     * Get the student that owns the absensi record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentWaliKelas::class, 'student_id');
    }
}
