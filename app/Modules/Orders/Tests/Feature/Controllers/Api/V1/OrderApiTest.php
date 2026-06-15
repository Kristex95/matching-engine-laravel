<?php

declare(strict_types=1);

namespace App\Modules\Orders\Tests\Feature\Controllers\Api\V1;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\Domain\Balance;
use App\Modules\Orders\Domain\Order;
use App\Modules\Orders\Tests\TestUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_index_structure(): void
    {
        $account = Account::factory()->create();
        $user = new TestUser([
            'id' => 1,
            'account_id' => $account->id,
        ]);
        $this->actingAs($user);

        $order = Order::factory()->create([
            'account_id' => $account->id,
        ]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'order_id',
                    'account_id',
                    'side',
                    'type',
                    'currency',
                    'price',
                    'amount',
                    'filled_amount',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_order_index_request_is_valid_and_exists(): void
    {
        $account = Account::factory()->create();
        $user = new TestUser([
            'id' => 1,
            'account_id' => $account->id,
        ]);
        $this->actingAs($user);

        $order = Order::factory()->create([
            'account_id' => $user->account_id,
        ]);

        $response = $this->getJson('/api/v1/orders?' . http_build_query([
            'account_id' => $order->account->id,
            'status' => $order->status,
            'side' => $order->side,
            'type' => $order->type,
            'currency' => $order->currency,
            'per_page' => 10,
        ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data',
            'meta',
            'links',
        ]);

        $response->assertJsonFragment([
            'order_id' => $order->uuid,
        ]);
    }

    public function test_user_only_sees_his_own_orders(): void
    {
        $account = Account::factory()->create();
        $user = new TestUser([
            'id' => 1,
            'account_id' => $account->id,
        ]);

        $otherAccount = Account::factory()->create();
        $otherUser = new TestUser([
            'id' => 2,
            'account_id' => $otherAccount->id,
        ]);

        $this->actingAs($user);

        $userOrder = Order::factory()->create([
            'account_id' => $user->account_id,
        ]);

        $otherUserOrder = Order::factory()->create([
            'account_id' => $otherUser->account_id,
        ]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertOk();

        $response->assertJsonFragment([
            'order_id' => $userOrder->uuid,
        ]);

        $response->assertJsonMissing([
            'order_id' => $otherUserOrder->uuid,
        ]);
    }

    public function test_order_store_returns_201_and_creates_order_when_valid(): void
    {
        $account = Account::factory()->create();
        $user = new TestUser([
            'id' => 1,
            'account_id' => $account->id,
        ]);
        $balance = Balance::factory()->create([
            'account_id' => $account->id,
            'currency'   => 'USDT',
            'available'  => '100000',
            'locked'     => '0',
        ]);
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/orders', [
            'side' => 'buy',
            'type' => 'limit',
            'currency' => 'BTC',
            "price" => 77000,
            "amount" => 1,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                "order_id",
                "account_id",
                "side",
                "type",
                "currency",
                "price",
                "amount",
                "filled_amount",
                "status",
                "created_at",
                "updated_at",
            ],
        ]);

        $this->assertDatabaseHas('orders', [
            'type'       => 'limit',
            'side'       => 'buy',
            'currency'   => 'BTC',
            'price'      => 77000,
            'amount'     => 1,
            'filled_amount' => 0,
            'status'     => 'new',
        ]);

        $this->assertDatabaseHas('active_orders', [
            'type'       => 'limit',
            'side'       => 'buy',
            'currency'   => 'BTC',
            'price'      => 77000,
            'amount'     => 1,
            'filled_amount' => 0,
            'status'     => 'new',
        ]);

        $this->assertEquals(
            DB::table('orders')->first(),
            DB::table('active_orders')->first()
        );
    }

    public function test_order_store_returns_422_when_creating_limit_order_without_price(): void
    {
        $user = new TestUser([
            'id' => 1,
        ]);
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/orders', [
            'side' => 'buy',
            'type' => 'limit',
            'currency' => 'BTC',
            "amount" => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            "message",
            "errors" => [
                "price",
            ],
        ]);
    }
}
