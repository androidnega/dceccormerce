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
            $table->string('media_kind', 16)->default('none')->after('value');
            $table->string('media_upload_path')->nullable()->after('media_kind');
            $table->string('media_external_url', 512)->nullable()->after('media_upload_path');
        });

        DB::table('promos')->whereNull('media_kind')->update(['media_kind' => 'none']);
    }

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['media_kind', 'media_upload_path', 'media_external_url']);
        });
    }
};
