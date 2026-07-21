<?php

namespace App\Models;

use Database\Factories\SikapFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'student_id',
    'beriman_bertakwa_dan_berakhlak_mulia',
    'mandiri',
    'bergotong_royong',
    'kreatif',
    'bernalar_kritis',
    'berkebinekaan_global',
])]
class Sikap extends Model
{
    /** @use HasFactory<SikapFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sikaps';

    /**
     * Get the student that owns the sikap.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentWaliKelas::class, 'student_id');
    }
}
