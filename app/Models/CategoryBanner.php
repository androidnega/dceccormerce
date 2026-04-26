<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'type',
    'title',
    'subtitle',
    'image_path',
    'image_width_percent',
    'image_offset_y',
    'video_path',
    'video_url',
    'background_color',
    'text_color',
    'cta_text',
    'link',
    'position',
    'active',
])]
class CategoryBanner extends Model
{
    public const TYPE_IMAGE = 'image_card';

    public const TYPE_VIDEO = 'video_card';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
    ];

    protected $table = 'category_banners';

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'active' => 'boolean',
            'image_width_percent' => 'integer',
            'image_offset_y' => 'integer',
        ];
    }

    /**
     * @param  Builder<CategoryBanner>  $query
     * @return Builder<CategoryBanner>
     */
    public function scopeActiveOrdered(Builder $query): Builder
    {
        return $query->where('active', true)->orderBy('position')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        $path = trim((string) $this->image_path);
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'images/') || str_starts_with($path, '/images/')) {
            return asset(ltrim($path, '/'));
        }

        return Storage::disk('public')->url($path);
    }

    public function videoSourceUrl(): ?string
    {
        $url = trim((string) $this->video_url);
        if ($url !== '') {
            return $url;
        }

        $path = trim((string) $this->video_path);
        if ($path === '') {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function youtubeVideoId(): ?string
    {
        foreach ([$this->video_url, $this->videoSourceUrl()] as $candidate) {
            $id = self::extractYoutubeVideoId(is_string($candidate) ? $candidate : null);
            if ($id !== null) {
                return $id;
            }
        }

        return null;
    }

    private static function extractYoutubeVideoId(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return $m[1];
        }

        return null;
    }
}
