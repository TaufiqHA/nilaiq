<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            if (Auth::user()->roles->contains('name', 'super_admin')) {
                return true;
            } else {
                return false;
            }
        } elseif ($panel->getId() === 'waliKelas') {
            if (Auth::user()->roles->contains('name', 'wali_kelas')) {
                return true;
            } else {
                return false;
            }
        } elseif ($panel->getId() === 'mapel') {
            if(Auth::user()->roles->contains('name', 'guru_mapel')) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public function kelas(): HasOne {
        return $this->hasOne(Kelas::class);
    }

}
