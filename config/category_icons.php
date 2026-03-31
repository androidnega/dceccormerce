<?php

return [

    /**
     * Font Awesome 6 icon classes (prefix included) per category slug.
     * Used in storefront sidebar and admin homepage row labels.
     */
    'slug' => [
        'iphones' => 'fa-solid fa-mobile-screen',
        'macbooks' => 'fa-solid fa-laptop',
        'airpods' => 'fa-solid fa-headphones',
        'accessories' => 'fa-solid fa-plug',
        'ipads' => 'fa-solid fa-tablet-screen-button',
        'apple-watch' => 'fa-solid fa-clock',
        'apple-tv' => 'fa-solid fa-tv',
        'apple-vision' => 'fa-solid fa-vr-cardboard',
        'homepod' => 'fa-solid fa-volume-high',
    ],

    /** When slug is unknown, match substrings in the category name (lowercase). */
    'name_contains' => [
        'iphone' => 'fa-solid fa-mobile-screen',
        'ipad' => 'fa-solid fa-tablet-screen-button',
        'macbook' => 'fa-solid fa-laptop',
        'mac ' => 'fa-solid fa-laptop',
        'airpod' => 'fa-solid fa-headphones',
        'apple watch' => 'fa-solid fa-clock',
        'watch' => 'fa-solid fa-clock',
        'apple tv' => 'fa-solid fa-tv',
        'apple vision' => 'fa-solid fa-vr-cardboard',
        'vision' => 'fa-solid fa-vr-cardboard',
        'homepod' => 'fa-solid fa-volume-high',
        'accessory' => 'fa-solid fa-plug',
        'cable' => 'fa-solid fa-plug',
        'case' => 'fa-solid fa-mobile-screen',
    ],

    'default' => 'fa-solid fa-tag',

];
