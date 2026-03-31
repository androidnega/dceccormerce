<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Point seed paths at committed placeholders when the original files are missing from public/.
     */
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('category_banners')) {
            return;
        }

        $publicBase = public_path('images');
        $map = [
            'images/apple-iphone-14-product-red-guenstig-gebraucht-kaufen.webp' => 'images/category-flagship.svg',
            'images/iPhone-16-Template.jpg' => 'images/category-stay-connected.svg',
        ];

        foreach ($map as $oldPath => $newPath) {
            $file = public_path($oldPath);
            if (is_file($file)) {
                continue;
            }
            DB::table('category_banners')
                ->where('image_path', $oldPath)
                ->update(['image_path' => $newPath, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Intentionally empty: cannot restore missing file paths.
    }
};
