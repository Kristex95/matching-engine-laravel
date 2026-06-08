<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Tests\Feature;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Users\Domain\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_share_same_account(): void
    {
        // Create one shared account
        $account = Account::factory()->create();

        // Create two users linked to the same account
        $user1 = User::factory()->create([
            'account_id' => $account->id,
        ]);

        $user2 = User::factory()->create([
            'account_id' => $account->id,
        ]);

        // Assert both users point to same account
        $this->assertEquals(
            $user1->account_id,
            $user2->account_id
        );
        $this->assertTrue($account->users->contains($user1));
        $this->assertTrue($account->users->contains($user2));
    }
}
