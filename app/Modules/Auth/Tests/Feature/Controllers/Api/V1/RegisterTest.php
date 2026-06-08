<?php

declare(strict_types=1);

namespace App\Modules\Auth\Tests\Feature\Controllers\Api\V1;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Users\Domain\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_and_account_are_created_successfully(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                    'account' => [
                        'id',
                        'account_number',
                    ],
                    'token',
                ],
            ]);

        // Ensure user exists
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        // Ensure account exists (adjust table name if needed)
        $this->assertDatabaseCount('accounts', 1);
    }

    public function test_user_is_linked_to_account(): void
    {
        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];

        $response = $this->postJson('/api/v1/register', $payload);

        $response->assertCreated();

        $user = User::where('email', 'jane@example.com')->firstOrFail();
        $account = Account::firstOrFail();

        $this->assertEquals($account->id, $user->account_id);
    }

    public function test_registration_returns_token(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Token User',
            'email' => 'token@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertCreated();

        $this->assertNotEmpty($response->json('data.token'));
    }
}
