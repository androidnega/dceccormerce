<?php

namespace Database\Seeders;

use App\Models\CategoryBanner;
use Illuminate\Database\Seeder;

class CategoryBannerSeeder extends Seeder
{
    public function run(): void
    {
        if (CategoryBanner::query()->exists()) {
            return;
        }

        CategoryBanner::query()->create([
            'type' => CategoryBanner::TYPE_IMAGE,
            'title' => 'Apple Watch & wearables',
            'subtitle' => 'Fitness, alerts, and style on your wrist',
            'image_path' => 'images/category-flagship.svg',
            'background_color' => '#b06a70',
            'text_color' => '#ffffff',
            'cta_text' => 'Shop Now',
            'link' => '/products?category=apple-watch',
            'position' => 0,
            'active' => true,
        ]);

        CategoryBanner::query()->create([
            'type' => CategoryBanner::TYPE_IMAGE,
            'title' => 'MacBooks & laptops',
            'subtitle' => 'Power and portability for every day',
            'image_path' => 'images/category-stay-connected.svg',
            'background_color' => '#6f8f86',
            'text_color' => '#ffffff',
            'cta_text' => 'Shop Now',
            'link' => '/products?category=macbooks',
            'position' => 10,
            'active' => true,
        ]);

        CategoryBanner::query()->create([
            'type' => CategoryBanner::TYPE_VIDEO,
            'title' => 'Street & summer vibes',
            'subtitle' => 'Skate, explore, and live outdoors',
            'video_url' => 'https://www.youtube.com/watch?v=TN70fjHPdCo',
            'background_color' => '#000000',
            'text_color' => '#ffffff',
            'cta_text' => 'Shop Now',
            'link' => '/products',
            'position' => 20,
            'active' => true,
        ]);
    }
}

