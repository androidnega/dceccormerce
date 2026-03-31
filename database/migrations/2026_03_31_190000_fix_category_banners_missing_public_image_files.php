<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * If image_path points at public/images/* but the file is missing, use committed SVG placeholders.
     */
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('category_banners')) {
            return;
        }

        $rows = DB::table('category_banners')
            ->where('type', 'image_card')
            ->orderBy('id')
            ->get(['id', 'image_path']);

        $i = 0;
        foreach ($rows as $row) {
            $path = trim((string) $row->image_path);
            if ($path === '' || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                continue;
            }

            $exists = false;
            if (str_starts_with($path, 'images/') || str_starts_with($path, '/images/')) {
                $exists = is_file(public_path(ltrim($path, '/')));
            } else {
                $exists = is_file(Storage::disk('public')->path($path));
            }

            if ($exists) {
                continue;
            }

            $fallback = ($i === 0)
                ? 'images/category-flagship.svg'
                : 'images/category-stay-connected.svg';
            $i++;

            DB::table('category_banners')
                ->where('id', $row->id)
                ->update(['image_path' => $fallback, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        //
    }
};
