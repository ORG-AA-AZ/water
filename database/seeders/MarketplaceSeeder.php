<?php

namespace Database\Seeders;

use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user =  User::factory()->create();

        Marketplace::create([
            'name' => fake()->name(),
            'mobile' => $user->mobile,
            'mobile_verified_at' => now(),
            'user_id' => $user->id,
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ]);
    }
}
