<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::query()->firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => Coupon::TYPE_PERCENT,
                'value' => 10,
                'usage_limit' => 1000,
                'used_count' => 0,
                'expires_at' => null,
                'is_active' => true,
            ]
        );
    }
}
