<?php

namespace Tests\Feature;

use App\Http\Controllers\User\LoginRequest;
use App\Http\Controllers\User\RegisterRequest;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\VerifyMobileNumber\NewVerifyCodeRequest;
use App\Http\Controllers\VerifyMobileNumber\VerifyRequest;
use App\Models\User;
use App\Resources\UserResource;
use Database\Factories\UserFactory;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserController::class)]
#[CoversClass(LoginRequest::class)]
#[CoversClass(RegisterRequest::class)]
#[CoversClass(UserResource::class)]
#[CoversClass(VerifyRequest::class)]
#[CoversClass(NewVerifyCodeRequest::class)]

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private Generator $faker;

    public function testRegisterUser(): void
    {
        $this->faker = Factory::create();

        $data = [
            'name' => $name = $this->faker->name(),
            'mobile' => $mobile = (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $this->postJson('/api/auth/user-register', $data)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'mobile',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'mobile' => $mobile,
        ]);

        $user = User::where('mobile', $mobile)->first();

        $this->assertNotNull($user->mobile_verification_code);
        $this->assertNull($user->mobile_verified_at);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    public function testLoginUser(): void
    {
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => 'password',
        ];

        $this->postJson('/api/auth/user-login', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'mobile',
                    'token',
                ],
            ]);
    }

    public function testVerifyMobileNumber(): void
    {
        $user = UserFactory::new()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'code' => $user->mobile_verification_code,
        ];

        $this->postJson('/api/auth/user-verify-mobile', $data)
            ->assertOk()
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Mobile number verified successfully',
            ]);
    }
}
