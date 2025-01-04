<?php

namespace App\Models;

use Filament\Panel;
use App\Models\waliKelas;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class teachers extends User
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
}
