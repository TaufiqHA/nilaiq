<?php

namespace App\Models;

use Database\Factories\SettingsWaliKelasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'school_name',
    'npsn',
    'school_address',
    'principal_name',
    'school_logo',
    'teacher_name',
    'teacher_nip',
    'teacher_email',
    'teacher_phone',
    'academicYear_id',
    'tanggal_penerimaan_rapor',
])]
class SettingsWaliKelas extends Model
{
    /** @use HasFactory<SettingsWaliKelasFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings_wali_kelas';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_penerimaan_rapor' => 'date',
        ];
    }

    /**
     * Get the academic year associated with the setting.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academicYear_id');
    }
}
