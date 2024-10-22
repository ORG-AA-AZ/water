<?php

namespace Database\Factories;

use App\Models\Marketplace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Marketplace>
 */
class MarketplaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Marketplace::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'national_id' => fake()->numberBetween(1000000000, 9999999999),
            'mobile' => fake()->numberBetween(1000000000, 9999999999),
            'mobile_verification_code' => Str::random(6),
            'mobile_verified_at' => null,
            'password' => static::$password ??= Hash::make('password'),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'mobile_verification_code' => null,
            'mobile_verified_at' => now(),
        ]);
    }

    public function forLocation(float $latitude, float $longitude): static
    {
        return $this->state(fn () => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }
}
