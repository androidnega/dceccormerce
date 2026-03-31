<?php

use App\Models\HomepageSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('homepage_sections')
            ->where('type', HomepageSection::TYPE_CATEGORY_BLOCK)
            ->where('image', 'images/category-block-hero.png')
            ->update(['image' => null, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Intentionally empty; do not restore removed asset reference.
    }
};
