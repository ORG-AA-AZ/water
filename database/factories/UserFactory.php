<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'mobile' => (string) fake()->numberBetween(1000000000, 9999999999),
            'mobile_verification_code' => Str::random(6),
            'mobile_verified_at' => null,
            'password' => static::$password ??= Hash::make('password'),
            'reset_password' => null,
            'remember_token' => Str::random(10),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];
    }

    /**
     * Indicate that the model's mobile address should be unverified.
     */
    public function verified(): static
    {
        return $this->state(fn () => [
            'mobile_verification_code' => null,
            'mobile_verified_at' => now(),
        ]);
    }

    public function setResetPassword(): static
    {
        return $this->state(fn () => [
            'reset_password' => Hash::make('reset_password'),
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
