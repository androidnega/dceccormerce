<?php

use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Shop-by-category rows should show three tiles (2 narrow + 1 wide hero).
     */
    public function up(): void
    {
        $third = [
            'title' => 'AirPods',
            'subtitle' => 'Feel the Music, Live the Moment',
            'image' => 'images/category-shop-hero.png',
            'link' => '/products?category=airpods',
            'layout' => 'wide',
            'cta_label' => 'Shop now',
        ];

        HomepageSection::query()
            ->where('type', HomepageSection::TYPE_CATEGORY_BLOCK)
            ->get()
            ->each(function (HomepageSection $section) use ($third) {
                $config = is_array($section->config) ? $section->config : [];
                $items = $config['items'] ?? [];
                if (! is_array($items) || count($items) !== 2) {
                    return;
                }

                $items[] = $third;
                $config['items'] = $items;
                $section->update(['config' => $config]);
            });
    }

    public function down(): void
    {
        // Optional: remove only the appended third tile if it matches defaults.
        HomepageSection::query()
            ->where('type', HomepageSection::TYPE_CATEGORY_BLOCK)
            ->get()
            ->each(function (HomepageSection $section) {
                $config = is_array($section->config) ? $section->config : [];
                $items = $config['items'] ?? [];
                if (! is_array($items) || count($items) !== 3) {
                    return;
                }
                $last = $items[2] ?? [];
                if (($last['title'] ?? '') === 'AirPods' && ($last['image'] ?? '') === 'images/category-shop-hero.png') {
                    array_pop($items);
                    $config['items'] = $items;
                    $section->update(['config' => $config]);
                }
            });
    }
};
