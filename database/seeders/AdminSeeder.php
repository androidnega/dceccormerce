<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@dcapple.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => '12345678',
                'role' => 'admin',
            ]
        );
    }
}
