<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Store catalog categories (nine rows for the demo sidebar).
     *
     * @return array<string, string> slug => display name
     */
    public static function definitions(): array
    {
        return [
            'accessories' => 'Accessories',
            'airpods' => 'AirPods',
            'apple-tv' => 'Apple TV',
            'apple-watch' => 'Apple Watch',
            'homepod' => 'HomePod',
            'iphones' => 'iPhones',
            'ipads' => 'iPads',
            'macbooks' => 'MacBooks',
            'vision' => 'Apple Vision',
        ];
    }

    public function run(): void
    {
        foreach (self::definitions() as $slug => $name) {
            Category::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
    }
}
