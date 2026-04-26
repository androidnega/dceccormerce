<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['product_id', 'image_path', 'sort_order', 'color_label'])]
class ProductImage extends Model
{
    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function url(): string
    {
        return static::resolveUrl($this->image_path);
    }

    /**
     * Local storage paths or absolute http(s) URLs (e.g. seeded catalog images).
     * Uses a root-relative /storage/... URL so WebP and other files load even when
     * APP_URL does not match the browser host (e.g. localhost vs 127.0.0.1).
     */
    public static function resolveUrl(?string $path): string
    {
        return public_storage_url($path);
    }

    protected static function booted(): void
    {
        static::deleting(function (ProductImage $image) {
            $path = $image->image_path;
            if ($path === null || $path === '') {
                return;
            }
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return;
            }

            Storage::disk('public')->delete($path);
        });
    }
}
