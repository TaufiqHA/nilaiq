<?php

namespace App\Models;

use Database\Factories\FinalExamsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'class_id',
    'title',
    'exam_date',
    'description',
])]
class FinalExams extends Model
{
    /** @use HasFactory<FinalExamsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    /**
     * Get the class that owns the final exam.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
