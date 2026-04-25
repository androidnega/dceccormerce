<?php

return [

    /** ISO 4217 code — all storefront amounts are in this currency. */
    'currency_code' => 'GHS',

    /** Display symbol for Ghana cedis (matches format_ghs / format_cedis). */
    'currency_symbol' => '₵',

    'phone' => env('STORE_PHONE', '+233 54 409 6292'),

    /** E.164-style number for tel: and WhatsApp (digits / leading +). */
    'phone_tel' => env('STORE_PHONE_TEL', '+233544096292'),

    'email' => env('STORE_EMAIL', 'support@dcapple.com'),

    'welcome_prefix' => env('STORE_WELCOME_PREFIX', 'Welcome to'),

    /** Shown in the storefront header top bar (optional). */
    'address' => env('STORE_ADDRESS', ''),

    /**
     * Social profile URLs for the header top bar. Use # or empty string to hide a slot.
     *
     * @var array<string, string>
     */
    'social' => [
        'facebook' => env('STORE_SOCIAL_FACEBOOK', '#'),
        'instagram' => env('STORE_SOCIAL_INSTAGRAM', '#'),
        'tiktok' => env('STORE_SOCIAL_TIKTOK', '#'),
        'youtube' => env('STORE_SOCIAL_YOUTUBE', '#'),
        'pinterest' => env('STORE_SOCIAL_PINTEREST', '#'),
    ],

    /** Minimum cart total for free shipping (displayed on homepage service bar). */
    'free_shipping_min' => (float) env('STORE_FREE_SHIPPING_MIN', 100),

    /**
     * Homepage "On sale" strip: one image per card (paths under public/). Card 1 → first file, etc.
     */
    'sale_spotlight_images' => [
        'images/6-540x540.jpg',
        'images/6-1-540x540.jpg',
        'images/29-300x300.jpg',
    ],

];
