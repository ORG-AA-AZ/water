<?php

namespace Tests\Feature;

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Marketplace\MarketplaceRequest;
use App\Models\User;
use App\Resources\MarketplaceResource;
use App\Resources\UserResource;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(MarketplaceController::class)]
#[CoversClass(MarketplaceRequest::class)]
#[CoversClass(MarketplaceResource::class)]
#[CoversClass(UserResource::class)]

class MarketplaceControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    /**
     * Test the store method.
     *
     * @return void
     */
    public function testStoreMarketplace()
    {
        $this->faker = Factory::create();

        // Define the data for the request
        $data = [
            'name' => $name = $this->faker->name(),
            'mobile' => $mobile = (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        // Make the POST request to the store method
        $resposne = $this->postJson(
            '/api/marketplace',
            $data
        );

        $resposne->assertStatus(201);

        $resposne->assertJsonStructure([
            'data' => [
                'id',
                'user' => [
                    'id',
                    'name',
                    'mobile',
                ],
            ],
        ]);

        // Assert that the marketplace and user are created in the database
        $this->assertDatabaseHas('marketplaces', [
            'name' => $name,
            'mobile' => $mobile,
        ]);

        $user = User::where('mobile', $mobile)->first();

        $this->assertTrue(Hash::check($password, $user->password));
    }
}
