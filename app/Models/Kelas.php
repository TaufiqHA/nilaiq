<?php

namespace App\Models;

use App\Models\Scopes\KelasScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $guarded = ['id'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function tahunAjaran() : BelongsTo {
        return $this->belongsTo(TahunAjaran::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new KelasScope);
    }
}
