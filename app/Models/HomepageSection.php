<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'type',
    'title',
    'subtitle',
    'image',
    'link',
    'config',
    'position',
    'is_active',
])]
class HomepageSection extends Model
{
    public const TYPE_SLIDER = 'slider';

    public const TYPE_CATEGORY_BLOCK = 'category_block';

    public const TYPE_FEATURED_PROMO = 'featured_promo';

    public const TYPE_PRODUCT_GRID = 'product_grid';

    public const TYPE_SOFT_PROMO = 'soft_promo';

    public const TYPE_FLASH_SECTION = 'flash_section';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_SLIDER,
        self::TYPE_CATEGORY_BLOCK,
        self::TYPE_FEATURED_PROMO,
        self::TYPE_PRODUCT_GRID,
        self::TYPE_SOFT_PROMO,
        self::TYPE_FLASH_SECTION,
    ];

    /**
     * Section types shown in the admin dropdown (category_block is managed under Shop by category).
     *
     * @return list<string>
     */
    public static function typesForAdmin(?string $currentType = null): array
    {
        if ($currentType === self::TYPE_CATEGORY_BLOCK) {
            return self::TYPES;
        }

        return array_values(array_filter(
            self::TYPES,
            static fn (string $t): bool => $t !== self::TYPE_CATEGORY_BLOCK
        ));
    }

    /**
     * @param  Builder<HomepageSection>  $query
     * @return Builder<HomepageSection>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<HomepageSection>  $query
     * @return Builder<HomepageSection>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function resolvedLink(): ?string
    {
        $l = trim((string) $this->link);

        return $l !== '' ? $l : null;
    }

    public function imageUrl(): ?string
    {
        $path = trim((string) $this->image);
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, '/images/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'homepage-sections/') || str_starts_with($path, '/homepage-sections/')) {
            return '/storage/'.ltrim($path, '/');
        }

        return public_storage_url($path);
    }

    /**
     * @return list<array{
     *   title: string,
     *   subtitle?: string,
     *   image?: string,
     *   link?: string,
     *   aspect?: string,
     *   text_size?: string,
     *   layout?: string,
     *   bg_color?: string,
     *   cta_label?: string
     * }>
     */
    public function categoryItems(): array
    {
        $items = $this->config['items'] ?? [];
        if (! is_array($items)) {
            return [];
        }

        $out = [];
        foreach ($items as $row) {
            if (! is_array($row)) {
                continue;
            }
            $t = trim((string) ($row['title'] ?? ''));
            if ($t === '') {
                continue;
            }
            $out[] = [
                'title' => $t,
                'subtitle' => isset($row['subtitle']) ? trim((string) $row['subtitle']) : '',
                'image' => isset($row['image']) ? trim((string) $row['image']) : '',
                'link' => isset($row['link']) ? trim((string) $row['link']) : '',
                // Optional tile styling presets (used by `home/sections/category_block.blade.php`).
                // Examples: 'aspect' => '4/3' | 'square' | 'portrait' | 'wide'
                //            'text_size' => 'sm' | 'md' | 'lg'
                //            'layout' => 'narrow' | 'wide' (empty = auto from grid position)
                //            'bg_color' => '#9b5a63' (solid tiles)
                //            'cta_label' => 'Shop now'
                'aspect' => isset($row['aspect']) ? trim((string) $row['aspect']) : '',
                'text_size' => isset($row['text_size']) ? trim((string) $row['text_size']) : '',
                'layout' => isset($row['layout']) ? trim((string) $row['layout']) : '',
                'bg_color' => isset($row['bg_color']) ? trim((string) $row['bg_color']) : '',
                'cta_label' => isset($row['cta_label']) ? trim((string) $row['cta_label']) : '',
            ];
        }

        return $out;
    }

    public static function assetUrlForPath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'images/') || str_starts_with($path, '/images/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'homepage-sections/')) {
            // Use a host-agnostic relative URL so tiles work even if APP_URL isn't set
            // to the same host that serves the storefront.
            return '/storage/'.ltrim($path, '/');
        }

        return asset(ltrim($path, '/'));
    }
}
