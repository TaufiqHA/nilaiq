<?php

namespace App\Models;

use Database\Factories\AcademicYearFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'year',
    'semester',
    'is_active',
    'user_id',
])]
class AcademicYear extends Model
{
    /** @use HasFactory<AcademicYearFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the active academic year owned by the given user.
     *
     * Never falls back to another user's data: without an explicit owner
     * there is no active academic year.
     */
    public static function getActive(?int $userId = null): ?self
    {
        $userId = $userId ?? auth()->id();

        if (! $userId) {
            return null;
        }

        return self::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    /**
     * Limit the query to records owned by the given user.
     */
    public function scopeOwnedBy(Builder $query, User|int $user): Builder
    {
        return $query->where('user_id', $user instanceof User ? $user->id : $user);
    }
}
