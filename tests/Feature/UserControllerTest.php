<?php

namespace Tests\Feature;

use App\Http\Controllers\LoginAndRegisterService\LoginAndRegisterService;
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
            ->assertJson([
                'status' => 'success',
                'message' => 'Account registered successfully. Verify your mobile number',
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

    public function testFailRegisterUserWithExistMobile(): void
    {
        $this->faker = Factory::create();
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'name' => $this->faker->name(),
            'mobile' => $user->mobile,
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $this->postJson('/api/auth/user-register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The mobile number has already been taken.',
                'errors' => [
                    'mobile' => ['The mobile number has already been taken.'],
                ],
            ]);
    }

    public function testFailRegisterUserNoneConfirmedPassword(): void
    {
        $this->faker = Factory::create();

        $data = [
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => Str::random(),
            'password_confirmation' => Str::random(),
        ];

        $this->postJson('/api/auth/user-register', $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The password confirmation does not match.',
                'errors' => [
                    'password' => ['The password confirmation does not match.'],
                ],
            ]);
    }

    public function testFailRegisterUserThrowsException(): void
    {
        $this->faker = Factory::create();

        $data = [
            'name' => $this->faker->name(),
            'mobile' => (string) $this->faker->unique()->numberBetween(1000000000, 9999999999),
            'password' => $password = Str::random(),
            'password_confirmation' => $password,
        ];

        $mocked_service = $this->createMock(LoginAndRegisterService::class);
        $mocked_service->expects($this->once())
            ->method('register')
            ->willThrowException(new \Exception('Registration error'));

        $this->app->instance(LoginAndRegisterService::class, $mocked_service);

        $this->postJson('/api/auth/user-register', $data)
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Registration error',
            ]);
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

    public function testInvalidLoginUserIfUserNotExistOrIncorrectPassword(): void
    {
        $user = UserFactory::new()->verified()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => Str::random(8),
        ];

        $this->postJson('/api/auth/user-login', $data)
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid login credentials',
            ]);
    }

    public function testInvalidLoginUserIfMobileNotVerified(): void
    {
        $user = UserFactory::new()->createOne();

        $data = [
            'mobile' => $user->mobile,
            'password' => 'password',
        ];

        $this->postJson('/api/auth/user-login', $data)
            ->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Your mobile number is not verified',
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

    public function testLogoutSuccessfully(): void
    {
        $user = UserFactory::new()->verified()->createOne();
        $user->createToken('API TOKEN')->plainTextToken;

        $this->actingAs($user)->deleteJson('/api/auth/user-logout')
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logged out successfully.',
            ]);

        $this->assertCount(0, $user->tokens);
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
