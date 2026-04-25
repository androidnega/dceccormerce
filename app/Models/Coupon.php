<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'type', 'value', 'usage_limit', 'used_count', 'expires_at', 'is_active'])]
class Coupon extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    public const TYPE_FREE_DELIVERY = 'free_delivery';

    /** @var list<string> */
    public const TYPES = [self::TYPE_PERCENT, self::TYPE_FIXED, self::TYPE_FREE_DELIVERY];

    /**
     * @param  Builder<Coupon>  $query
     * @return Builder<Coupon>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
