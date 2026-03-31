<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    protected static function bootGeneratesUniqueSlug(): void
    {
        static::saving(function (Model $model) {
            if (! $model instanceof static) {
                return;
            }

            if (! $model->exists || $model->isDirty('name') || blank($model->slug)) {
                $base = Str::slug((string) $model->name);
                if ($base === '') {
                    $base = 'item';
                }
                $model->slug = static::uniqueSlug($base, $model->getKey());
            }
        });
    }

    public static function uniqueSlug(string $base, ?int $exceptId = null): string
    {
        $slug = $base;
        $n = 1;

        while (static::query()
            ->when($exceptId !== null, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }
}
