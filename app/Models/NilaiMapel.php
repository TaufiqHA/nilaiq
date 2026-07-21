<?php

namespace App\Models;

use Database\Factories\NilaiMapelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'student_id',
    'mapel_id',
    'nilai',
    'capaian',
])]
class NilaiMapel extends Model
{
    /** @use HasFactory<NilaiMapelFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nilai_mapels';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
        ];
    }

    /**
     * Get the student that owns the nilai mapel record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentWaliKelas::class, 'student_id');
    }

    /**
     * Get the mapel setting that owns the nilai mapel record.
     */
    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MapelSettings::class, 'mapel_id');
    }
}
