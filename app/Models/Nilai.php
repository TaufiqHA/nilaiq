<?php

namespace App\Models;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Scopes\NilaiScope;
use App\Models\student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nilai extends Model
{
    protected $guarded = ['id'];

    public function student() : BelongsTo {
        return $this->belongsTo(student::class)->withoutGlobalScopes();
    }

    public function mapel() : BelongsTo {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas() : BelongsTo {
        return $this->belongsTo(Kelas::class)->withoutGlobalScopes();
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new NilaiScope);
    }
}
