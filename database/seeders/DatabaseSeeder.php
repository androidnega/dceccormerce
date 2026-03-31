<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            ManagerSeeder::class,
            HomepageSettingSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            HeroSlideSeeder::class,
            HomepageSectionSeeder::class,
            CategoryBannerSeeder::class,
            PromoSeeder::class,
            DeliveryRuleSeeder::class,
            DeliveryAgentSeeder::class,
        ]);
    }
}
