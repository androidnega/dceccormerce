<?php

use App\Models\SiteSetting;

if (! function_exists('format_ghs')) {
    /**
     * Format an amount as Ghana cedis (GHS), using the cedi symbol (₵).
     */
    function format_ghs(float|int|string|null $amount): string
    {
        return '₵'.number_format((float) $amount, 2);
    }
}

if (! function_exists('format_cedis')) {
    /**
     * @see format_ghs()
     */
    function format_cedis(float|int|string|null $amount): string
    {
        return format_ghs($amount);
    }
}

if (! function_exists('public_storage_url')) {
    /**
     * Root-relative URL for files on the public disk (served via public/storage → storage/app/public).
     *
     * Prefer this over Storage::disk('public')->url() in HTML so images use the current request host
     * when APP_URL is wrong or still localhost on production (common on shared hosting).
     */
    function public_storage_url(?string $path): string
    {
        if ($path === null || $path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        return '/storage/'.$path;
    }
}

if (! function_exists('category_fa_classes')) {
    /**
     * Font Awesome 6 classes for a category (e.g. for storefront sidebar and admin UI).
     */
    function category_fa_classes(?string $slug, ?string $name = null): string
    {
        $bySlug = config('category_icons.slug', []);
        if ($slug !== null && $slug !== '' && isset($bySlug[$slug])) {
            return $bySlug[$slug];
        }
        $n = strtolower((string) $name);
        foreach (config('category_icons.name_contains', []) as $needle => $classes) {
            if ($needle !== '' && str_contains($n, $needle)) {
                return $classes;
            }
        }

        return (string) config('category_icons.default', 'fa-solid fa-tag');
    }
}

if (! function_exists('category_mega_image_url')) {
    /**
     * Hero image URL for storefront mega menu columns (slug config, then name hints, then default).
     *
     * @param  string|null  $pathOrUrl  Path under public/ or https URL
     */
    function category_mega_image_url(?string $slug, ?string $name = null): string
    {
        $resolve = function (mixed $pathOrUrl): string {
            $s = (string) $pathOrUrl;

            return str_starts_with($s, 'http://') || str_starts_with($s, 'https://') ? $s : asset($s);
        };

        $bySlug = config('mega_nav.slug', []);
        if ($slug !== null && $slug !== '' && isset($bySlug[$slug])) {
            return $resolve($bySlug[$slug]);
        }

        $n = strtolower((string) $name);
        foreach (config('mega_nav.name_contains', []) as $needle => $pathOrUrl) {
            if ($needle !== '' && str_contains($n, $needle)) {
                return $resolve($pathOrUrl);
            }
        }

        return $resolve(config('mega_nav.default', 'images/category-flagship.svg'));
    }
}

if (! function_exists('mega_nav_decor_url')) {
    function mega_nav_decor_url(string $key): string
    {
        $decor = config('mega_nav.decor', []);
        $pathOrUrl = $decor[$key] ?? null;
        if ($pathOrUrl === null && is_array($decor) && $decor !== []) {
            $pathOrUrl = reset($decor);
        }
        if ($pathOrUrl === null || $pathOrUrl === false) {
            $pathOrUrl = config('mega_nav.default', 'images/category-flagship.svg');
        }

        return str_starts_with((string) $pathOrUrl, 'http://') || str_starts_with((string) $pathOrUrl, 'https://')
            ? (string) $pathOrUrl
            : asset((string) $pathOrUrl);
    }
}

if (! function_exists('paystack_public_key')) {
    function paystack_public_key(): string
    {
        return (string) (SiteSetting::get('paystack_public_key') ?: env('PAYSTACK_PUBLIC_KEY', ''));
    }
}

if (! function_exists('paystack_secret_key')) {
    function paystack_secret_key(): string
    {
        return (string) (SiteSetting::get('paystack_secret_key') ?: env('PAYSTACK_SECRET_KEY', ''));
    }
}

if (! function_exists('paystack_enabled_from_settings')) {
    function paystack_enabled_from_settings(): bool
    {
        $stored = SiteSetting::get('paystack_enabled');

        if ($stored !== null && $stored !== '') {
            return $stored === '1' || $stored === 'true';
        }

        return filter_var(env('PAYSTACK_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('paystack_ready')) {
    function paystack_ready(): bool
    {
        if (! paystack_enabled_from_settings()) {
            return false;
        }

        return paystack_public_key() !== '' && paystack_secret_key() !== '';
    }
}

if (! function_exists('sms_notifications_enabled')) {
    function sms_notifications_enabled(): bool
    {
        $stored = SiteSetting::get('sms_notifications_enabled');

        if ($stored !== null && $stored !== '') {
            return $stored === '1' || $stored === 'true';
        }

        return filter_var(env('SMS_NOTIFICATIONS_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('whatsapp_notifications_enabled')) {
    function whatsapp_notifications_enabled(): bool
    {
        $stored = SiteSetting::get('whatsapp_notifications_enabled');

        if ($stored !== null && $stored !== '') {
            return $stored === '1' || $stored === 'true';
        }

        return filter_var(env('WHATSAPP_NOTIFICATIONS_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }
}

if (! function_exists('sms_provider_resolved')) {
    function sms_provider_resolved(): string
    {
        $stored = SiteSetting::get('sms_provider');
        if ($stored !== null && $stored !== '') {
            return (string) $stored;
        }

        return (string) config('sms.provider', 'log');
    }
}

if (! function_exists('sms_api_key_resolved')) {
    function sms_api_key_resolved(): string
    {
        $fromDb = SiteSetting::get('sms_api_key');

        return (string) (($fromDb !== null && $fromDb !== '') ? $fromDb : (config('sms.api_key') ?? env('SMS_API_KEY', '')));
    }
}

if (! function_exists('sms_sender_resolved')) {
    function sms_sender_resolved(): string
    {
        $stored = SiteSetting::get('sms_sender');
        if ($stored !== null && $stored !== '') {
            return (string) $stored;
        }

        return (string) config('sms.sender', 'DCAPPLE');
    }
}

if (! function_exists('sms_arkesel_url_resolved')) {
    function sms_arkesel_url_resolved(): string
    {
        $stored = SiteSetting::get('sms_arkesel_url');
        if ($stored !== null && $stored !== '') {
            return (string) $stored;
        }

        return (string) config('sms.arkesel_url', 'https://sms.arkesel.com/sms/api');
    }
}

if (! function_exists('arkesel_sms_ready')) {
    function arkesel_sms_ready(): bool
    {
        if (sms_provider_resolved() !== 'arkesel') {
            return false;
        }

        return sms_api_key_resolved() !== '' && trim(sms_sender_resolved()) !== '';
    }
}
