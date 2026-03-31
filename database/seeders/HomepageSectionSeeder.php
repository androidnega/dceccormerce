<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use Illuminate\Database\Seeder;

class HomepageSectionSeeder extends Seeder
{
    public function run(): void
    {
        if (HomepageSection::query()->exists()) {
            return;
        }

        HomepageSection::query()->create([
            'type' => HomepageSection::TYPE_SLIDER,
            'title' => 'Today only — Free delivery in Accra on qualifying orders',
            'subtitle' => '',
            'image' => '',
            'link' => '/products',
            'config' => null,
            'position' => 0,
            'is_active' => true,
        ]);

        HomepageSection::query()->create([
            'type' => HomepageSection::TYPE_CATEGORY_BLOCK,
            'title' => 'Shop by category',
            'subtitle' => 'Large visuals, calm layout — pick your lane.',
            'image' => null,
            'link' => null,
            'config' => [
                'items' => [
                    [
                        'title' => 'MacBook',
                        'subtitle' => 'Pro power for work & study',
                        'image' => 'images/category-macbook.png',
                        'link' => '/products?category=macbooks',
                        'layout' => 'narrow',
                        'bg_color' => '#9b5a63',
                        'cta_label' => 'Shop now',
                    ],
                    [
                        'title' => 'iPhone',
                        'subtitle' => 'Latest models · Unlocked',
                        'image' => 'images/category-iphone.png',
                        'link' => '/products?category=iphones',
                        'layout' => 'narrow',
                        'bg_color' => '#5f7d75',
                        'cta_label' => 'Shop now',
                    ],
                    [
                        'title' => 'AirPods',
                        'subtitle' => 'Feel the Music, Live the Moment',
                        'image' => 'images/category-shop-hero.png',
                        'link' => '/products?category=airpods',
                        'layout' => 'wide',
                        'cta_label' => 'Shop now',
                    ],
                ],
            ],
            'position' => 10,
            'is_active' => true,
        ]);

        HomepageSection::query()->create([
            'type' => HomepageSection::TYPE_FEATURED_PROMO,
            'title' => 'Elegant collection',
            'subtitle' => 'Designed for a modern lifestyle — premium devices, careful curation, and service you can trust.',
            'image' => 'images/ss2_copy_900x.webp',
            'link' => '/products',
            'config' => null,
            'position' => 20,
            'is_active' => true,
        ]);

        HomepageSection::query()->create([
            'type' => HomepageSection::TYPE_PRODUCT_GRID,
            'title' => 'New arrivals',
            'subtitle' => 'Fresh stock in ' . config('store.currency_code'),
            'image' => null,
            'link' => null,
            'config' => [
                'source' => 'latest',
                'limit' => 8,
            ],
            'position' => 30,
            'is_active' => true,
        ]);

        HomepageSection::query()->create([
            'type' => HomepageSection::TYPE_SOFT_PROMO,
            'title' => 'Limited pieces available',
            'subtitle' => 'When they are gone, they are gone — secure yours while stock lasts.',
            'image' => null,
            'link' => '/products',
            'config' => null,
            'position' => 40,
            'is_active' => true,
        ]);

        HomepageSection::query()->create([
            'type' => HomepageSection::TYPE_FLASH_SECTION,
            'title' => 'Trending now',
            'subtitle' => 'What shoppers are viewing this week',
            'image' => null,
            'link' => null,
            'config' => [
                'source' => 'trending',
                'limit' => 4,
            ],
            'position' => 50,
            'is_active' => true,
        ]);
    }
}
