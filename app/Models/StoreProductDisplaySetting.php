<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreProductDisplaySetting extends Model
{
    public const LAYOUT_GRID = 'grid';

    public const LAYOUT_LIST = 'list';

    public const LAYOUT_CAROUSEL = 'carousel';

    public const LAYOUT_MASONRY = 'masonry';

    public const LAYOUT_COMPACT = 'compact';

    /** Minimal marketplace-style card: square image, divider, price row + heart, stars, title. */
    public const LAYOUT_SLEEK = 'sleek';

    public const CARD_SMALL = 'small';

    public const CARD_MEDIUM = 'medium';

    public const CARD_LARGE = 'large';

    /** Featured block on the products catalog page: responsive grid (default). */
    public const FEATURED_GRID = 'grid';

    /** Horizontal scrolling product strip. */
    public const FEATURED_CAROUSEL = 'carousel';

    /** Large hero-style slideshow (Apple-like) with arrows and dots. */
    public const FEATURED_SHOWCASE = 'showcase';

    protected $table = 'store_product_display_settings';

    protected $fillable = [
        'product_layout',
        'enable_hover_actions',
        'enable_quick_view',
        'enable_wishlist',
        'enable_image_hover_swap',
        'card_size',
        'featured_products_display',
    ];

    protected function casts(): array
    {
        return [
            'enable_hover_actions' => 'boolean',
            'enable_quick_view' => 'boolean',
            'enable_wishlist' => 'boolean',
            'enable_image_hover_swap' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'product_layout' => self::LAYOUT_GRID,
                'enable_hover_actions' => true,
                'enable_quick_view' => true,
                'enable_wishlist' => true,
                'enable_image_hover_swap' => false,
                'card_size' => self::CARD_MEDIUM,
                'featured_products_display' => self::FEATURED_GRID,
            ]
        );
    }

    /**
     * @return list<string>
     */
    public static function layoutOptions(): array
    {
        return [
            self::LAYOUT_GRID,
            self::LAYOUT_LIST,
            self::LAYOUT_CAROUSEL,
            self::LAYOUT_MASONRY,
            self::LAYOUT_COMPACT,
            self::LAYOUT_SLEEK,
        ];
    }

    /**
     * @return list<string>
     */
    public static function cardSizeOptions(): array
    {
        return [
            self::CARD_SMALL,
            self::CARD_MEDIUM,
            self::CARD_LARGE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function featuredProductsDisplayOptions(): array
    {
        return [
            self::FEATURED_GRID,
            self::FEATURED_CAROUSEL,
            self::FEATURED_SHOWCASE,
        ];
    }
}
