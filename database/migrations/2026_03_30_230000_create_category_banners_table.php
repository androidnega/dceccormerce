<?php

use App\Models\CategoryBanner;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_banners', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32)->default(CategoryBanner::TYPE_IMAGE);
            $table->string('title', 255);
            $table->string('subtitle', 512)->nullable();
            $table->string('image_path', 512)->nullable();
            $table->string('video_path', 512)->nullable();
            $table->string('video_url', 2048)->nullable();
            $table->string('background_color', 32)->nullable(); // e.g. #b76e79
            $table->string('text_color', 32)->nullable();       // e.g. #ffffff
            $table->string('cta_text', 64)->nullable();
            $table->string('link', 2048)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_banners');
    }
};
