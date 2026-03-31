<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'category',
    'headline',
    'published_at',
    'image_path',
    'link_url',
    'sort_order',
    'is_active',
])]
class NewsPost extends Model
{
    /**
     * @param  Builder<NewsPost>  $query
     * @return Builder<NewsPost>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<NewsPost>  $query
     * @return Builder<NewsPost>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }

    public function resolveImageUrl(): string
    {
        $path = trim((string) $this->image_path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'news-images/')) {
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

    public function resolveLinkUrl(): string
    {
        $url = trim((string) ($this->link_url ?? '/'));
        if ($url === '') {
            return url('/');
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
        if (str_starts_with($url, '/') && str_contains($url, '#')) {
            [$path, $frag] = explode('#', $url, 2);

            return url($path).'#'.$frag;
        }

        return url($url);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
