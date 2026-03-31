<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'title',
    'type',
    'value',
    'media_kind',
    'media_upload_path',
    'media_external_url',
    'is_active',
    'sort_order',
    'homepage_slot',
])]
class Promo extends Model
{
    public const TYPE_BANNER = 'banner';

    public const TYPE_DISCOUNT = 'discount';

    public const TYPE_FREE_DELIVERY = 'free_delivery';

    public const MEDIA_NONE = 'none';

    public const MEDIA_IMAGE = 'image';

    public const MEDIA_VIDEO = 'video';

    /** Upper strip on home (after trust bar) */
    public const SLOT_SECONDARY = 'secondary';

    /** Main grid (after Mac / iPhone banners) */
    public const SLOT_PRIMARY = 'primary';

    /** @var list<string> */
    public const TYPES = [self::TYPE_BANNER, self::TYPE_DISCOUNT, self::TYPE_FREE_DELIVERY];

    /** @var list<string> */
    public const MEDIA_KINDS = [self::MEDIA_NONE, self::MEDIA_IMAGE, self::MEDIA_VIDEO];

    /** @var list<string> */
    public const HOMEPAGE_SLOTS = [self::SLOT_PRIMARY, self::SLOT_SECONDARY];

    /**
     * @param  Builder<Promo>  $query
     * @return Builder<Promo>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Promo>  $query
     * @return Builder<Promo>
     */
    public function scopeBanners(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_BANNER);
    }

    /**
     * @param  Builder<Promo>  $query
     * @return Builder<Promo>
     */
    public function scopeHomepageSlot(Builder $query, string $slot): Builder
    {
        return $query->where('homepage_slot', $slot);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function hasHeroImage(): bool
    {
        return $this->media_kind === self::MEDIA_IMAGE
            && ($this->media_upload_path || trim((string) $this->media_external_url) !== '');
    }

    public function hasHeroVideo(): bool
    {
        return $this->media_kind === self::MEDIA_VIDEO && trim((string) $this->media_external_url) !== '';
    }

    public function heroImageUrl(): ?string
    {
        if (! $this->hasHeroImage()) {
            return null;
        }

        if ($this->media_upload_path) {
            return Storage::disk('public')->url($this->media_upload_path);
        }

        return trim((string) $this->media_external_url);
    }

    public function videoPageUrl(): ?string
    {
        if (! $this->hasHeroVideo()) {
            return null;
        }

        return trim((string) $this->media_external_url);
    }

    public function youtubeEmbedSrc(): ?string
    {
        $url = $this->videoPageUrl();
        if ($url === null || $url === '') {
            return null;
        }

        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }

        return null;
    }

    public function vimeoEmbedSrc(): ?string
    {
        $url = $this->videoPageUrl();
        if ($url === null || $url === '') {
            return null;
        }

        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return null;
    }

    public function isDirectVideoFile(): bool
    {
        $url = $this->videoPageUrl();
        if ($url === null || $url === '') {
            return false;
        }

        if ($this->youtubeEmbedSrc() !== null || $this->vimeoEmbedSrc() !== null) {
            return false;
        }

        return (bool) preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $url);
    }

    public function bannerLinkHref(): string
    {
        $raw = trim((string) $this->value);
        if ($raw === '') {
            return route('products.index');
        }

        if (preg_match('#^https?://#i', $raw)) {
            return $raw;
        }

        if (str_starts_with($raw, '/')) {
            $parts = explode('#', $raw, 2);

            return url($parts[0]).(isset($parts[1]) ? '#'.$parts[1] : '');
        }

        return url('/'.$raw);
    }

    public static function activeCartDiscountPercent(): float
    {
        $row = self::query()
            ->active()
            ->where('type', self::TYPE_DISCOUNT)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($row === null) {
            return 0.0;
        }

        $v = (float) $row->value;

        return max(0.0, min(100.0, $v));
    }

    public static function hasActiveFreeDeliveryPromo(): bool
    {
        return self::query()
            ->active()
            ->where('type', self::TYPE_FREE_DELIVERY)
            ->exists();
    }
}
