<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;

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
