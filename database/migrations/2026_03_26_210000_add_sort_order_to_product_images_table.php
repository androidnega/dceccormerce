<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('image_path');
        });

        $rows = DB::table('product_images')->orderBy('product_id')->orderBy('id')->get();
        $productId = null;
        $n = 0;
        foreach ($rows as $row) {
            if ($productId !== $row->product_id) {
                $productId = $row->product_id;
                $n = 0;
            }
            DB::table('product_images')->where('id', $row->id)->update(['sort_order' => $n]);
            $n++;
        }
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
