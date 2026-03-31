<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Store manager account: sign in with username "manager" or email manager@dcapple.com.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager',
                'email' => 'manager@dcapple.com',
                'password' => 'admin123',
                'role' => 'manager',
            ]
        );
    }
}
