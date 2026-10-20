<?php

namespace App\Models;

use Database\Factories\EkskulFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'student_id',
    'ekskul1',
    'ekskul2',
    'ekskul3',
])]
class Ekskul extends Model
{
    /** @use HasFactory<EkskulFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ekskuls';

    /**
     * Get the student that owns the ekskul.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentWaliKelas::class, 'student_id');
    }

    /**
     * Limit the query to records owned by the given user.
     */
    public function scopeOwnedBy(Builder $query, User|int $user): Builder
    {
        return $query->whereHas(
            'student.classWaliKelas',
            fn (Builder $relation): Builder => $relation->where('user_id', $user instanceof User ? $user->id : $user),
        );
    }
}
