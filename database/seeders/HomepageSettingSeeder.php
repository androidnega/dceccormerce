<?php

namespace Database\Seeders;

use App\Models\HomepageSetting;
use Illuminate\Database\Seeder;

class HomepageSettingSeeder extends Seeder
{
    public function run(): void
    {
        HomepageSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'homepage_layout' => HomepageSetting::LAYOUT_CAROUSEL,
                'promo_banners' => HomepageSetting::defaultPromoBanners(),
            ]
        );
    }
}
