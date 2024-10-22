<?php

namespace Database\Seeders;

use App\Models\Marketplace;
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
        Marketplace::create([
            'national_id' => fake()->numberBetween(1000000000, 9999999999),
            'name' => fake()->name(),
            'mobile' => fake()->numberBetween(1000000000, 9999999999),
            'mobile_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'latitude' => 29.533224,
            'longitude' => 35.006957,
        ]);
    }
}
