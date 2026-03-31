<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    protected $fillable = [
        'sort_order',
        'subheading',
        'headline',
        'headline_line2',
        'cta_label',
        'cta_url',
        'product_id',
        'image_path',
        'background_hex',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Root-relative URL so images work when APP_URL differs from the browser host
     * (e.g. http://127.0.0.1:8000 vs http://localhost:8000).
     */
    public function imageUrl(): ?string
    {
        if ($this->image_path === null || $this->image_path === '') {
            return null;
        }

        $path = str_replace('\\', '/', $this->image_path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        return '/storage/'.$path;
    }

    public function resolveCtaUrl(): string
    {
        if ($this->product_id !== null && $this->relationLoaded('product') && $this->product !== null) {
            return route('products.show', $this->product);
        }

        if ($this->product_id !== null) {
            $this->loadMissing('product');
            if ($this->product !== null) {
                return route('products.show', $this->product);
            }
        }

        $url = $this->cta_url;
        if ($url === null || $url === '') {
            return '#';
        }
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }
        if (str_starts_with($url, '#')) {
            if ($url === '#store-search') {
                return route('products.index').'#store-search';
            }

            return route('home').$url;
        }

        return url($url);
    }
}
