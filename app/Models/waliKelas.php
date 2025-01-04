<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;

class waliKelas extends User
{
    protected $fillable = [
        'name',
        'nip',
        'email',
        'class_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function class()
    {
        return $this->belongsTo(classes::class);
    }
}
