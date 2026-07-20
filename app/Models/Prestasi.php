<?php

namespace App\Models;

use Database\Factories\PrestasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'student_id',
    'prestasi1',
    'catatan_prestasi1',
    'prestasi2',
    'catatan_prestasi2',
    'prestasi3',
    'catatan_prestasi3',
])]
class Prestasi extends Model
{
    /** @use HasFactory<PrestasiFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prestasis';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prestasi1' => 'integer',
            'prestasi2' => 'integer',
            'prestasi3' => 'integer',
        ];
    }

    /**
     * Get the student that owns the prestasi record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentWaliKelas::class, 'student_id');
    }
}
