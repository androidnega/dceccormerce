<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_banners', function (Blueprint $table) {
            $table->unsignedTinyInteger('image_width_percent')->default(96)->after('image_path');
            $table->smallInteger('image_offset_y')->default(-56)->after('image_width_percent');
        });
    }

    public function down(): void
    {
        Schema::table('category_banners', function (Blueprint $table) {
            $table->dropColumn(['image_width_percent', 'image_offset_y']);
        });
    }
};
