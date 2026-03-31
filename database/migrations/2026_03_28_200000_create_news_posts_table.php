<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_posts', function (Blueprint $table) {
            $table->id();
            $table->string('category', 64);
            $table->string('headline');
            $table->date('published_at');
            $table->string('image_path', 512);
            $table->string('link_url', 2048)->default('/');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $y = (int) now()->format('Y');
        DB::table('news_posts')->insert([
            [
                'category' => 'Hi-Tech',
                'headline' => 'New iPhone lineup: what to expect',
                'published_at' => "{$y}-03-12",
                'image_path' => 'images/12_374d37ef-6951-4c61-b46d-af6244139c32_1024x1024.webp',
                'link_url' => '/products#store-search',
                'sort_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Business',
                'headline' => 'Why genuine Apple gear matters for work',
                'published_at' => "{$y}-03-08",
                'image_path' => 'images/b2_900x.webp',
                'link_url' => '/products#store-search',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category' => 'Tips',
                'headline' => 'Getting the most from your Mac battery',
                'published_at' => "{$y}-03-01",
                'image_path' => 'images/ss2_copy_900x.webp',
                'link_url' => '/products#store-search',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('news_posts');
    }
};
