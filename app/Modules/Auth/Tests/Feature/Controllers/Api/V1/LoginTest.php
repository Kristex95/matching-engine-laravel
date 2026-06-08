<?php

declare(strict_types=1);

namespace Modules\Auth\Tests\Feature\Controllers\Api\V1;

use App\Modules\Users\Domain\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user can login and receive a bearer token.
     */
    public function test_user_can_login_and_receive_token(): void
    {
        $password = 'Password123';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'token',
            ],
        ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    /**
     * Test that accessing orders without bearer token is forbidden.
     */
    public function test_orders_endpoint_requires_bearer_token(): void
    {
        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(401);
    }
}
