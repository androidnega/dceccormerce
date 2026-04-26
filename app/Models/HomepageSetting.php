<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomepageSetting extends Model
{
    public const LAYOUT_CAROUSEL = 'carousel';

    public const LAYOUT_SIDEBAR = 'sidebar';

    /** Premium stacked card hero (center focus, overlapping siblings). */
    public const LAYOUT_STACKED_CARDS = 'stacked_cards';

    protected $fillable = [
        'homepage_layout',
        'stacked_cards_stage_bg_hex',
        'hero_fullwidth_bg_hex',
        'hero_fullwidth_text_hex',
        'sidebar_category_ids',
        'promo_banners',
    ];

    protected function casts(): array
    {
        return [
            'sidebar_category_ids' => 'array',
            'promo_banners' => 'array',
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    public static function defaultPromoBanners(): array
    {
        return [
            [
                'kicker' => 'Watch Series',
                'headline' => 'Apple Watch',
                'title_line1' => 'Watch Series',
                'title_line2' => 'Apple Watch',
                'price_label' => '₵ 319',
                'image_path' => 'images/category-flagship.svg',
                'background_hex' => '#ffeb3b',
                'link_url' => '/',
            ],
            [
                'kicker' => 'New Step',
                'headline' => 'Apple iPhone',
                'title_line1' => 'New Step',
                'title_line2' => 'Apple iPhone',
                'price_label' => '₵ 599',
                'image_path' => 'images/category-stay-connected.svg',
                'background_hex' => '#ffeb3b',
                'link_url' => '/',
            ],
            [
                'kicker' => 'Featured',
                'headline' => 'More deals',
                'title_line1' => 'Featured',
                'title_line2' => 'More deals',
                'price_label' => 'FROM ₵ 99',
                'image_path' => 'images/logo.svg',
                'background_hex' => '#ffeb3b',
                'link_url' => '/',
            ],
        ];
    }

    /**
     * Legacy admin data sometimes used "$" for promo price lines; storefront uses Ghana cedis (₵).
     */
    public static function normalizePromoPriceLabel(?string $label): string
    {
        $label = trim((string) $label);

        return str_replace('$', '₵', $label);
    }

    /**
     * Three promo cards merged with defaults (for display and admin).
     *
     * @return list<array<string, string>>
     */
    public function mergedPromoBanners(): array
    {
        $defaults = self::defaultPromoBanners();
        $stored = $this->promo_banners;
        if (! is_array($stored)) {
            return $defaults;
        }

        $out = [];
        foreach (range(0, 2) as $i) {
            $merged = array_merge($defaults[$i], is_array($stored[$i] ?? null) ? $stored[$i] : []);
            $merged['price_label'] = self::normalizePromoPriceLabel($merged['price_label'] ?? '');

            $out[] = $merged;
        }

        return $out;
    }

    public function resolvePromoImageUrl(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'promo-banners/')) {
            return '/storage/'.$path;
        }
        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        if (Str::contains($path, '/')) {
            return '/'.Str::beforeLast($path, '/').'/'.rawurlencode(Str::afterLast($path, '/'));
        }

        return '/'.rawurlencode($path);
    }

    public function resolvePromoLink(?string $url): string
    {
        $url = $url ?? '/';
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

    /**
     * @return list<array{kicker: string, headline: string, title: list{0: string, 1: string}, price: string, image: string, alt: string, background_hex: string, href: string}>
     */
    public function promoBannersForView(): array
    {
        $rows = [];
        foreach ($this->mergedPromoBanners() as $r) {
            $t1 = trim((string) ($r['title_line1'] ?? ''));
            $t2 = trim((string) ($r['title_line2'] ?? ''));
            $kicker = trim((string) ($r['kicker'] ?? ''));
            $headline = trim((string) ($r['headline'] ?? ''));
            if ($headline === '') {
                $headline = $t2 !== '' ? $t2 : $t1;
            }
            if ($kicker === '' && $t1 !== '' && $t2 !== '' && $t1 !== $t2) {
                $kicker = $t1;
                $headline = $t2;
            }
            $rows[] = [
                'kicker' => $kicker,
                'headline' => $headline,
                'title' => [$t1, $t2],
                'price' => self::normalizePromoPriceLabel((string) ($r['price_label'] ?? '')),
                'image' => $this->resolvePromoImageUrl($r['image_path'] ?? ''),
                'alt' => trim($kicker.' '.$headline) ?: trim($t1.' '.$t2),
                'background_hex' => (string) ($r['background_hex'] ?? '#ffeb3b'),
                'href' => $this->resolvePromoLink($r['link_url'] ?? '/'),
            ];
        }

        return $rows;
    }

    /**
     * @return list<int|null>
     */
    public function sidebarCategoryIdsPadded(): array
    {
        $raw = $this->sidebar_category_ids ?? [];

        if (! is_array($raw)) {
            return array_fill(0, 9, null);
        }

        $ids = array_values(array_map(fn ($v) => $v !== null && $v !== '' ? (int) $v : null, $raw));

        return array_pad(array_slice($ids, 0, 9), 9, null);
    }

    /**
     * Nine slots for the demo sidebar. Configured IDs are resolved first; any
     * remaining null slots are filled with other categories (by name) not already
     * shown. If every admin slot is empty, starts from the first nine categories
     * by name, then the same back-fill applies when fewer than nine exist in DB.
     *
     * @return Collection<int, Category|null>
     */
    public function resolvedSidebarCategories(): Collection
    {
        $ids = $this->sidebarCategoryIdsPadded();
        $hasAny = collect($ids)->contains(fn ($v) => $v !== null);

        if (! $hasAny) {
            $first = Category::query()->orderBy('name')->limit(9)->pluck('id')->all();
            $ids = array_slice(array_pad($first, 9, null), 0, 9);
        }

        $nonNull = array_values(array_filter($ids, fn ($v) => $v !== null));
        $map = $nonNull === []
            ? collect()
            : Category::query()->whereIn('id', $nonNull)->get()->keyBy('id');

        $resolved = [];
        $usedIds = [];

        foreach (range(0, 8) as $i) {
            $id = $ids[$i] ?? null;
            $cat = ($id !== null) ? $map->get($id) : null;
            if ($cat !== null) {
                $usedIds[$cat->id] = true;
            }
            $resolved[$i] = $cat;
        }

        $pool = Category::query()
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $c) => ! isset($usedIds[$c->id]))
            ->values();

        $p = 0;
        foreach (range(0, 8) as $i) {
            if ($resolved[$i] !== null) {
                continue;
            }
            if ($p >= $pool->count()) {
                break;
            }
            $c = $pool[$p];
            $p++;
            $usedIds[$c->id] = true;
            $resolved[$i] = $c;
        }

        return collect($resolved);
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'homepage_layout' => self::LAYOUT_CAROUSEL,
                'stacked_cards_stage_bg_hex' => '#0a0a0a',
                'hero_fullwidth_bg_hex' => '#f7f9fd',
                'hero_fullwidth_text_hex' => '#0b1628',
                'promo_banners' => self::defaultPromoBanners(),
            ]
        );
    }

    /** Sanitized #RRGGBB for the stacked card carousel stage (defaults to near-black). */
    public function stackedCardsStageBgHex(): string
    {
        $raw = trim((string) ($this->stacked_cards_stage_bg_hex ?? ''));

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $raw) ? $raw : '#0a0a0a';
    }

    /** True when the stage background is light — use dark chrome for nav and dots. */
    public function stackedCardsStageChromeLight(): bool
    {
        return self::hexIsLight($this->stackedCardsStageBgHex());
    }

    /** @return array{0: int, 1: int, 2: int}|null */
    public static function parseHexRgb(string $hex): ?array
    {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return null;
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    public static function hexIsLight(string $hex): bool
    {
        $rgb = self::parseHexRgb($hex);
        if ($rgb === null) {
            return true;
        }
        $lin = static fn (float $c): float => $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        $R = $lin($rgb[0] / 255);
        $G = $lin($rgb[1] / 255);
        $B = $lin($rgb[2] / 255);
        $luminance = 0.2126 * $R + 0.7152 * $G + 0.0722 * $B;

        return $luminance > 0.55;
    }

    public static function rgbaFromHex(string $hex, float $alpha): string
    {
        $rgb = self::parseHexRgb($hex);
        if ($rgb === null) {
            return 'rgba(0,0,0,'.max(0, min(1, $alpha)).')';
        }
        $a = max(0.0, min(1.0, $alpha));

        return sprintf('rgba(%d,%d,%d,%s)', $rgb[0], $rgb[1], $rgb[2], (string) round($a, 4));
    }

    /** Full-width hero background (carousel layout). */
    public function heroFullwidthBgHex(): string
    {
        $raw = trim((string) ($this->hero_fullwidth_bg_hex ?? ''));

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $raw) ? $raw : '#f7f9fd';
    }

    /** Full-width hero headline / primary slide text. */
    public function heroFullwidthTextHex(): string
    {
        $raw = trim((string) ($this->hero_fullwidth_text_hex ?? ''));

        return preg_match('/^#[0-9A-Fa-f]{6}$/', $raw) ? $raw : '#0b1628';
    }

    public function heroFullwidthBgIsLight(): bool
    {
        return self::hexIsLight($this->heroFullwidthBgHex());
    }

    /** Slightly darker than background for bottom border. */
    public function heroFullwidthBorderHex(): string
    {
        $rgb = self::parseHexRgb($this->heroFullwidthBgHex());
        if ($rgb === null) {
            return '#d2deef';
        }
        $f = 0.88;
        $r = (int) max(0, min(255, round($rgb[0] * $f)));
        $g = (int) max(0, min(255, round($rgb[1] * $f)));
        $b = (int) max(0, min(255, round($rgb[2] * $f)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /** Muted subline color derived from primary text. */
    public function heroFullwidthSubColor(): string
    {
        return self::rgbaFromHex($this->heroFullwidthTextHex(), 0.78);
    }

    /** Placeholder / empty-state hint on hero. */
    public function heroFullwidthMutedColor(): string
    {
        return self::rgbaFromHex($this->heroFullwidthTextHex(), 0.42);
    }

    /** Inactive carousel dot (full-width). */
    public function heroFullwidthDotIdleRgba(): string
    {
        return $this->heroFullwidthBgIsLight()
            ? 'rgba(15,23,42,0.28)'
            : 'rgba(255,255,255,0.38)';
    }
}
