<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'name',
    'slug',
    'description',
    'price',
    'discount_type',
    'discount_value',
    'flash_sale',
    'sale_end_time',
    'is_featured',
    'is_trending',
    'stock',
    'is_active',
])]
class Product extends Model
{
    use GeneratesUniqueSlug;

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'flash_sale' => 'boolean',
            'sale_end_time' => 'datetime',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
        ];
    }

    public function listPrice(): float
    {
        return round((float) $this->price, 2);
    }

    public function hasActiveDiscount(): bool
    {
        if ($this->discount_type === null || $this->discount_type === '') {
            return false;
        }

        $v = (float) ($this->discount_value ?? 0);

        if ($v <= 0) {
            return false;
        }

        if ($this->discount_type === 'percent') {
            return $v < 100;
        }

        return $this->listPrice() > 0 && $v < $this->listPrice();
    }

    public function effectivePrice(): float
    {
        $base = $this->listPrice();

        if (! $this->hasActiveDiscount()) {
            return $base;
        }

        if ($this->discount_type === 'percent') {
            $pct = min(100.0, (float) $this->discount_value);

            return round(max(0, $base * (1 - $pct / 100)), 2);
        }

        return round(max(0, $base - (float) $this->discount_value), 2);
    }

    public function discountBadgeLabel(): ?string
    {
        if (! $this->hasActiveDiscount()) {
            return null;
        }

        if ($this->discount_type === 'percent') {
            return '-'.(int) round((float) $this->discount_value).'%';
        }

        return '-'.format_ghs((float) $this->discount_value);
    }

    public function flashSaleCountdownActive(): bool
    {
        if (! $this->flash_sale) {
            return false;
        }

        return $this->sale_end_time !== null && $this->sale_end_time->isFuture();
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images()->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
