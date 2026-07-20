<?php

namespace App\Models;

use Database\Factories\MapelSettingsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'settingsWaliKelas_id',
    'mapel',
    'guru',
    'kkm',
])]
class MapelSettings extends Model
{
    /** @use HasFactory<MapelSettingsFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapel_settings';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kkm' => 'integer',
        ];
    }

    /**
     * Get the settings wali kelas that owns the mapel settings.
     */
    public function settingsWaliKelas(): BelongsTo
    {
        return $this->belongsTo(SettingsWaliKelas::class, 'settingsWaliKelas_id');
    }
}
