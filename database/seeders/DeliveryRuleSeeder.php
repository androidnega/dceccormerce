<?php

namespace Database\Seeders;

use App\Models\DeliveryRule;
use Illuminate\Database\Seeder;

class DeliveryRuleSeeder extends Seeder
{
    public function run(): void
    {
        // Don't duplicate if the admin already configured delivery rules.
        if (DeliveryRule::query()->exists()) {
            return;
        }

        $rules = [
            // Accra
            ['zone' => 'Accra', 'method' => 'rider', 'option' => 'standard', 'price' => 20.00, 'estimated_time' => '2–4 hours', 'active' => true],
            ['zone' => 'Accra', 'method' => 'rider', 'option' => 'express', 'price' => 40.00, 'estimated_time' => '1 hour', 'active' => true],
            ['zone' => 'Accra', 'method' => 'pickup', 'option' => 'pickup', 'price' => 0.00, 'estimated_time' => 'immediate', 'active' => true],

            // Takoradi
            ['zone' => 'Takoradi', 'method' => 'rider', 'option' => 'standard', 'price' => 18.00, 'estimated_time' => '2–4 hours', 'active' => true],
            ['zone' => 'Takoradi', 'method' => 'rider', 'option' => 'express', 'price' => 35.00, 'estimated_time' => '1 hour', 'active' => true],
            ['zone' => 'Takoradi', 'method' => 'pickup', 'option' => 'pickup', 'price' => 0.00, 'estimated_time' => 'immediate', 'active' => true],

            // Outside City
            ['zone' => 'Outside City', 'method' => 'rider', 'option' => 'standard', 'price' => 50.00, 'estimated_time' => '1–2 days', 'active' => true],
            ['zone' => 'Outside City', 'method' => 'rider', 'option' => 'express', 'price' => 80.00, 'estimated_time' => 'same day', 'active' => true],
            ['zone' => 'Outside City', 'method' => 'pickup', 'option' => 'pickup', 'price' => 0.00, 'estimated_time' => 'immediate', 'active' => true],
        ];

        foreach ($rules as $row) {
            DeliveryRule::query()->create($row);
        }
    }
}
