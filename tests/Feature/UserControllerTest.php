<?php

namespace Tests\Feature;

use App\Http\Controllers\User\LoginRequest;
use App\Http\Controllers\User\NewVerifyCodeRequest;
use App\Http\Controllers\User\RegisterRequest;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\VerifyRequest;
use App\Models\User;
use App\Resources\UserResource;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserController::class)]
#[CoversClass(LoginRequest::class)]
#[CoversClass(NewVerifyCodeRequest::class)]
#[CoversClass(RegisterRequest::class)]
#[CoversClass(VerifyRequest::class)]
#[CoversClass(UserResource::class)]

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    /**
     * Test the store method.
     *
     * @return void
     */
    public function testStoreUser()
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
            '/api/auth/register',
            $data
        );

        $resposne->assertStatus(201);

        $resposne->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'mobile',
            ],
        ]);

        // Assert that the marketplace and user are created in the database
        $this->assertDatabaseHas('users', [
            'name' => $name,
            'mobile' => $mobile,
        ]);

        $user = User::where('mobile', $mobile)->first();

        $this->assertTrue(Hash::check($password, $user->password));
    }
}
