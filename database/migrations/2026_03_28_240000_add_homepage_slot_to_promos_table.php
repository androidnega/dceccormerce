<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->string('homepage_slot', 24)->default('primary')->after('sort_order');
        });

        DB::table('promos')->where('type', 'free_delivery')->update(['homepage_slot' => 'secondary']);
    }

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn('homepage_slot');
        });
    }
};
