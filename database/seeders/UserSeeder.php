<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Abood Akram',
            'mobile' => '0797193116',
            'password' => 'password123',
            'mobile_verified_at' => now()
        ]);
    }
}
