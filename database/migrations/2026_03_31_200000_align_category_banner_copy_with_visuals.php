<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Align titles, subtitles, and links with the smartwatch, laptop, and lifestyle video cards.
     * Updates the first three rows in storefront order (position, then id).
     */
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('category_banners')) {
            return;
        }

        $rows = DB::table('category_banners')
            ->orderBy('position')
            ->orderBy('id')
            ->limit(3)
            ->pluck('id');

        $updates = [
            [
                'title' => 'Apple Watch & wearables',
                'subtitle' => 'Fitness, alerts, and style on your wrist',
                'link' => '/products?category=apple-watch',
            ],
            [
                'title' => 'MacBooks & laptops',
                'subtitle' => 'Power and portability for every day',
                'link' => '/products?category=macbooks',
            ],
            [
                'title' => 'Street & summer vibes',
                'subtitle' => 'Skate, explore, and live outdoors',
                'link' => '/products',
            ],
        ];

        foreach ($rows as $i => $id) {
            if (! isset($updates[$i])) {
                break;
            }
            DB::table('category_banners')
                ->where('id', $id)
                ->update(array_merge($updates[$i], ['updated_at' => now()]));
        }
    }

    public function down(): void
    {
        //
    }
};
