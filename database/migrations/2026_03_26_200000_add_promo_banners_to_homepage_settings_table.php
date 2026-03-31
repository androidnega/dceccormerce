<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->json('promo_banners')->nullable()->after('sidebar_category_ids');
        });

        $default = [
            [
                'kicker' => 'Watch Series',
                'headline' => 'Apple Watch',
                'title_line1' => 'Watch Series',
                'title_line2' => 'Apple Watch',
                'price_label' => 'FROM ₵ 319',
                'image_path' => 'images/hero-watch-showcase.png',
                'background_hex' => '#ffeb3b',
                'link_url' => '/',
            ],
            [
                'kicker' => 'New Step',
                'headline' => 'Apple iPhone',
                'title_line1' => 'New Step',
                'title_line2' => 'Apple iPhone',
                'price_label' => 'FROM ₵ 599',
                'image_path' => 'images/apple-iphone-14-product-red-guenstig-gebraucht-kaufen.webp',
                'background_hex' => '#ffeb3b',
                'link_url' => '/',
            ],
            [
                'kicker' => 'Featured',
                'headline' => 'More deals',
                'title_line1' => 'Featured',
                'title_line2' => 'More deals',
                'price_label' => 'FROM ₵ 99',
                'image_path' => 'images/b2_900x.webp',
                'background_hex' => '#ffeb3b',
                'link_url' => '/',
            ],
        ];

        if (DB::table('homepage_settings')->where('id', 1)->exists()) {
            DB::table('homepage_settings')->where('id', 1)->update([
                'promo_banners' => json_encode($default),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->dropColumn('promo_banners');
        });
    }
};
