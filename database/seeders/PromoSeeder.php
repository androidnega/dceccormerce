<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('promos')) {
            return;
        }

        if (DB::table('promos')->count() > 0) {
            return;
        }

        $now = now();

        $rows = [
            [
                'title' => 'Free nationwide delivery this week — no minimum.',
                'type' => 'free_delivery',
                'value' => '1',
                'media_kind' => 'none',
                'media_upload_path' => null,
                'media_external_url' => null,
                'is_active' => true,
                'sort_order' => 0,
                'homepage_slot' => 'secondary',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Spring sale: extra 5% off your cart at checkout.',
                'type' => 'discount',
                'value' => '5',
                'media_kind' => 'none',
                'media_upload_path' => null,
                'media_external_url' => null,
                'is_active' => true,
                'sort_order' => 1,
                'homepage_slot' => 'primary',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Trade-in bonus — ask us about store credit on your old device.',
                'type' => 'banner',
                'value' => '/products#store-search',
                'media_kind' => 'none',
                'media_upload_path' => null,
                'media_external_url' => null,
                'is_active' => true,
                'sort_order' => 2,
                'homepage_slot' => 'primary',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        if (! Schema::hasColumn('promos', 'homepage_slot')) {
            foreach ($rows as $i => $_) {
                unset($rows[$i]['homepage_slot']);
            }
        }

        DB::table('promos')->insert($rows);
    }
}
