<?php

namespace App\Models;

use Database\Factories\ClassWaliKelasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_year_id',
    'name',
    'user_id',
])]
class ClassWaliKelas extends Model
{
    /** @use HasFactory<ClassWaliKelasFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'class_wali_kelas';

    /**
     * Get the academic year that owns the class wali kelas.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Get the user (wali kelas) that owns the class.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the students for the class (wali kelas).
     */
    public function students(): HasMany
    {
        return $this->hasMany(StudentWaliKelas::class, 'class_id');
    }

    /**
     * Limit the query to classes owned by the given user.
     */
    public function scopeOwnedBy(Builder $query, User|int $user): Builder
    {
        return $query->where('user_id', $user instanceof User ? $user->id : $user);
    }
}
