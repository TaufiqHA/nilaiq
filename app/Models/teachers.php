<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\Rules\Exists;

class teachers extends User implements FilamentUser
{
    protected $fillable = [
        'name',
        'nip',
        'email',
        'phone_number',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function mapel()
    {
        return $this->hasOne(guruMataPelajaran::class);
    }

    public function academic_year()
    {
        return $this->belongsTo(academicYear::class, 'academic_year_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Ambil user yang sedang login
        $user = auth('teacher')->user();

        // Cek apakah ID user terdapat di tabel 'guruMataPelajaran'
        return guruMataPelajaran::where('teachers_id', $user->id)->exists();
    }
}
