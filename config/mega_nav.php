<?php

/**
 * Mega menu imagery: paths under public/ or absolute https URLs.
 * Categories have no DB image field; these keep the nav visually rich.
 */
return [

    'default' => 'images/category-flagship.svg',

    'slug' => [
        'iphones' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=480&h=320&fit=crop&q=80',
        'macbooks' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=480&h=320&fit=crop&q=80',
        'ipads' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=480&h=320&fit=crop&q=80',
        'airpods' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=480&h=320&fit=crop&q=80',
        'apple-watch' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=480&h=320&fit=crop&q=80',
        'apple-tv' => 'https://images.unsplash.com/photo-1593784991091-a04703163fcb?w=480&h=320&fit=crop&q=80',
        'apple-vision' => 'https://images.unsplash.com/photo-1622979135225-d2ba269fb1bd?w=480&h=320&fit=crop&q=80',
        'homepod' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=480&h=320&fit=crop&q=80',
        'accessories' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=480&h=320&fit=crop&q=80',
    ],

    'name_contains' => [
        'iphone' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=480&h=320&fit=crop&q=80',
        'ipad' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=480&h=320&fit=crop&q=80',
        'macbook' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=480&h=320&fit=crop&q=80',
        'airpod' => 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?w=480&h=320&fit=crop&q=80',
        'watch' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?w=480&h=320&fit=crop&q=80',
    ],

    /** Decorative blocks when a column has no category image */
    'decor' => [
        'a' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=480&h=320&fit=crop&q=80',
        'b' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=480&h=320&fit=crop&q=80',
        'c' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=480&h=320&fit=crop&q=80',
    ],

];
