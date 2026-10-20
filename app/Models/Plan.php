<?php

namespace App\Models;

use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'price',
    'original_price',
    'duration_days',
    'is_active',
])]
class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;

    public const ANNUAL_CODE = 'annual';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'original_price' => 'integer',
            'duration_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Limit the query to plans that can be purchased.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default annual plan, creating it when the database was
     * migrated without running the plan seeder.
     */
    public static function annual(): ?self
    {
        $plan = static::query()->where('code', self::ANNUAL_CODE)->first();

        if ($plan === null) {
            $plan = static::query()->create([
                'code' => self::ANNUAL_CODE,
                'name' => 'NilaiQ Tahunan',
                'price' => 150000,
                'original_price' => 500000,
                'duration_days' => 365,
                'is_active' => true,
            ]);
        }

        return $plan->is_active ? $plan : null;
    }

    /**
     * Get the subscriptions using this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the orders using this plan.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the discount percentage compared to the original price.
     */
    public function discountPercentage(): int
    {
        if ($this->original_price === null || $this->original_price <= $this->price) {
            return 0;
        }

        return (int) round((1 - ($this->price / $this->original_price)) * 100);
    }
}
