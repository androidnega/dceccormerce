<?php

namespace Database\Seeders;

use App\Models\DeliveryAgent;
use App\Models\Rider;
use Illuminate\Database\Seeder;

class DeliveryAgentSeeder extends Seeder
{
    public function run(): void
    {
        $riders = Rider::query()->orderBy('id')->get();

        foreach ($riders as $rider) {
            DeliveryAgent::query()->firstOrCreate(
                ['rider_id' => $rider->id],
                [
                    'name' => $rider->name,
                    'type' => 'rider',
                    'phone' => $rider->phone,
                    'vehicle_type' => $rider->vehicle_type,
                    'status' => 'available',
                ]
            );
        }
    }
}
