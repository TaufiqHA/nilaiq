<?php

namespace App\Models;

use App\Models\Scopes\AbsensiScope;
use App\Models\student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $guarded = ['id'];

    public function mapel(): BelongsTo {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas(): BelongsTo {
        return $this->belongsTo(Kelas::class)->withoutGlobalScopes();
    }

    public function student(): BelongsTo {
        return $this->belongsTo(student::class)->withoutGlobalScopes();
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new AbsensiScope);
    }
}
