<?php

namespace Tests\Feature;

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Marketplace;
use App\Resources\MarketplaceResource;
use Database\Factories\MarketplaceFactory;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(MarketplaceController::class)]
#[CoversClass(LoginRequest::class)]
#[CoversClass(ResetPasswordRequest::class)]
#[CoversClass(MarketplaceResource::class)]

class MarketplaceControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    public function testStoreMarketplace(): void
    {
        $this->faker = Factory::create();

        $data = [
            'national_id' => $national_id = Str::random(8),
            'name' => $name = $this->faker->name(),
            'mobile' => $mobile = (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
            'latitude' => $latitude = $this->faker->latitude(),
            'longitude' => $longitude = $this->faker->longitude(),
        ];

        $this->postJson('/api/auth/marketplace-register', $data)
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Account registered successfully. Verify your mobile number',
            ]);

        $this->assertDatabaseHas('marketplaces', [
            'national_id' => $national_id,
            'name' => $name,
            'mobile' => $mobile,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $marketplace = Marketplace::where('mobile', $mobile)->first();

        $this->assertTrue(Hash::check($password, $marketplace->password));
    }

    public function testFailStoreMarketplaceNoneConfirmedPassword(): void
    {
        $this->faker = Factory::create();

        $data = [
            'national_id' => Str::random(),
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => Str::random(),
            'password_confirmation' => Str::random(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),        ];

        $this->postJson('/api/auth/marketplace-register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The password confirmation does not match.',
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ],
            ]);
    }

    public function testFailStoreExistMarketplaceNationalId(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->verified()->createOne();

        $data = [
            'national_id' => (string) $marketplace->national_id,
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),        ];

        $this->postJson('/api/auth/marketplace-register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The national ID has already been taken.',
                'errors' => [
                    'national_id' => ['The national ID has already been taken.'],
                ],
            ]);
    }

    public function testFailStoreExistMarketplaceMobile(): void
    {
        $this->faker = Factory::create();
        $marketplace = MarketplaceFactory::new()->verified()->createOne();

        $data = [
            'national_id' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'name' => $this->faker->name(),
            'mobile' => (string) $marketplace->mobile,
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),        ];

        $this->postJson('/api/auth/marketplace-register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The mobile number has already been taken.',
                'errors' => [
                    'mobile' => ['The mobile number has already been taken.'],
                ],
            ]);
    }

    public function testLogoutSuccessfully(): void
    {
        $marketplace = MarketplaceFactory::new()->verified()->createOne();
        $token = $marketplace->createToken('API TOKEN')->plainTextToken;

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/auth/marketplace-logout')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logged out successfully.',
            ]);

        $this->assertCount(0, $marketplace->tokens);
        $this->assertCount(0, $marketplace->tokens);
    }

    public function testLogoutUnauthenticatedUser(): void
    {
        $this->deleteJson('/api/auth/user-logout')
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
