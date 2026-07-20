<?php

namespace App\Models;

use Database\Factories\SettingsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'school_name',
    'npsn',
    'school_address',
    'principal_name',
    'school_logo',
    'teacher_name',
    'teacher_nip',
    'teacher_email',
    'teacher_phone',
    'subject_name',
    'minimum_score',
    'daily_test_weight',
    'assignment_weight',
    'midterm_weight',
    'final_weight',
    'user_id',
])]
class Settings extends Model
{
    /** @use HasFactory<SettingsFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'minimum_score' => 'decimal:2',
            'daily_test_weight' => 'decimal:2',
            'assignment_weight' => 'decimal:2',
            'midterm_weight' => 'decimal:2',
            'final_weight' => 'decimal:2',
        ];
    }
}
