<?php

declare(strict_types=1);

namespace App\Modules\Orders\Tests\Feature\Controllers\Api\V1;

use App\Modules\Orders\Domain\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_index_structure(): void
    {
        $order = Order::factory()->create();

        $response = $this->getJson('/api/v1/orders');

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
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
        $order = Order::factory()->create();

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
            'id' => $order->id,
        ]);
    }
}
