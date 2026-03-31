<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->string('hero_fullwidth_bg_hex', 7)->default('#f7f9fd')->after('stacked_cards_stage_bg_hex');
            $table->string('hero_fullwidth_text_hex', 7)->default('#0b1628')->after('hero_fullwidth_bg_hex');
        });
    }

    public function down(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->dropColumn(['hero_fullwidth_bg_hex', 'hero_fullwidth_text_hex']);
        });
    }
};
