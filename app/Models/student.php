<?php

namespace App\Models;

use App\Models\Kelas;
use App\Models\Absensi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class student extends Model
{
    protected $guarded = ['id'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensi(): HasMany {
        return $this->hasMany(Absensi::class);
    }
}