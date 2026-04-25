<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    private const CACHE_KEY = 'site_settings_all';

    private static function secretKeys(): array
    {
        return [
            'paystack_secret_key',
            'sms_api_key',
        ];
    }

    public static function get(?string $key, mixed $default = null): mixed
    {
        if ($key === null || $key === '') {
            return $default;
        }

        $row = self::query()->where('key', $key)->first();

        if ($row === null || $row->value === null) {
            return $default;
        }

        if (in_array($key, self::secretKeys(), true)) {
            try {
                return Crypt::decryptString($row->value);
            } catch (\Throwable) {
                return $default;
            }
        }

        return $row->value;
    }

    public static function set(string $key, mixed $value): void
    {
        if ($value === null || $value === '') {
            self::query()->where('key', $key)->delete();
            Cache::forget(self::CACHE_KEY);

            return;
        }

        $stored = $value;
        if (in_array($key, self::secretKeys(), true)) {
            $stored = Crypt::encryptString((string) $value);
        }

        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => (string) $stored]
        );
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, string>
     */
    public static function allKeyed(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $out = [];
            foreach (self::query()->get() as $row) {
                $v = $row->value;
                if (in_array($row->key, self::secretKeys(), true) && $v !== null && $v !== '') {
                    try {
                        $v = Crypt::decryptString($v);
                    } catch (\Throwable) {
                        $v = '';
                    }
                }
                $out[$row->key] = (string) $v;
            }

            return $out;
        });
    }
}
