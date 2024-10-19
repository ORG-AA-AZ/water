<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'image' => fake()->imageUrl(640, 480, 'products'),
            'price' => fake()->randomFloat(2, 1, 100), // Price between 1 and 100
            'quantity' => fake()->numberBetween(1, 100),
            'factory' => fake()->randomDigitNotNull(), // Sample factory value
            'marketplace_id' => 1, // Assuming you have a marketplace with ID 1 or adjust accordingly
        ]);
    }
}
