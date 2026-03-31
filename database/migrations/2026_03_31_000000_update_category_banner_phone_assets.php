<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('category_banners')) {
            return;
        }

        DB::table('category_banners')
            ->where('position', 0)
            ->where('type', 'image_card')
            ->update([
                'image_path' => 'images/apple-iphone-14-product-red-guenstig-gebraucht-kaufen.webp',
                'background_color' => '#b06a70',
            ]);

        DB::table('category_banners')
            ->where('position', 10)
            ->where('type', 'image_card')
            ->update([
                'image_path' => 'images/iPhone-16-Template.jpg',
                'background_color' => '#6f8f86',
            ]);
    }

    public function down(): void
    {
        // Content rollback not applied.
    }
};
