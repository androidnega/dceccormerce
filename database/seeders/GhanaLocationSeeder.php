<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use App\Models\Region;
use Illuminate\Database\Seeder;

class GhanaLocationSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Greater Accra',
            'Ashanti',
            'Western',
            'Eastern',
            'Central',
            'Northern',
            'Upper East',
            'Upper West',
            'Volta',
            'Bono',
            'Bono East',
            'Ahafo',
            'Savannah',
            'North East',
            'Western North',
            'Oti',
        ];

        foreach ($names as $name) {
            Region::query()->firstOrCreate(['name' => $name]);
        }

        $byName = Region::query()->whereIn('name', $names)->get()->keyBy('name');

        $zones = [
            'Greater Accra' => [
                ['name' => 'Accra Central', 'fee' => 15],
                ['name' => 'Tema', 'fee' => 20],
                ['name' => 'Kasoa', 'fee' => 18],
                ['name' => 'Madina', 'fee' => 17],
            ],
            'Ashanti' => [
                ['name' => 'Kumasi', 'fee' => 20],
            ],
            'Western' => [
                ['name' => 'Takoradi', 'fee' => 15],
            ],
        ];

        foreach ($zones as $regionName => $rows) {
            $region = $byName->get($regionName);
            if ($region === null) {
                continue;
            }
            foreach ($rows as $row) {
                DeliveryZone::query()->firstOrCreate(
                    [
                        'region_id' => $region->id,
                        'name' => $row['name'],
                    ],
                    [
                        'fee' => $row['fee'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
