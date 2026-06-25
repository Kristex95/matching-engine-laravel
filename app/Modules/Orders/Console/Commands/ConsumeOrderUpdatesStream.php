<?php

declare(strict_types=1);

namespace App\Modules\Orders\Console\Commands;

use App\Modules\Orders\Application\DTO\OrderUpdateDTO;
use App\Modules\Orders\Application\Services\OrderService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ConsumeOrderUpdatesStream extends Command
{
    protected $signature = 'stream:consume-order-updates';

    protected $description = 'Consume incoming trades from the Redis trades-stream';

    public function __construct(
        private OrderService $orderService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $stream = 'orders-status-stream';
        $group = 'laravel-workers';
        $consumer = 'laravel-consumer-2';

        /** @var PhpRedisConnection $redis */
        $redis = Redis::connection();

        $this->info("Initializing consumer for stream: {$stream}...");

        try {
            $redis->executeRaw([
                'XGROUP',
                'CREATE',
                $stream,
                $group,
                '0',
                'MKSTREAM',
            ]);
        } catch (Exception $e) {
            // If the group already exists, Redis returns a 'BUSYGROUP' error. We safely ignore it.
            if (!str_contains($e->getMessage(), 'BUSYGROUP')) {
                Log::error("Redis stream group creation failed: " . $e->getMessage());
                $this->error("Could not initialize stream group. Check logs.");
                return;
            }
        }

        $this->info("Listening for new trades...");

        for (;;) {
            $response = $redis->executeRaw([
                'XREADGROUP',
                'GROUP',
                $group,
                $consumer,
                'COUNT',
                '10',
                'BLOCK',
                '2000',
                'STREAMS',
                $stream,
                '>',
            ]);

            if (empty($response) || !is_array($response)) {
                continue;
            }

            foreach ($response as $streamEntries) {
                if (!isset($streamEntries[1]) || !is_array($streamEntries[1])) {
                    continue;
                }

                foreach ($streamEntries[1] as $message) {
                    $messageId = $message[0];
                    $rawFields = $message[1];

                    // Rebuild the flat array [key, val, key, val] into an associative array
                    $payload = [];
                    for ($i = 0; $i < count($rawFields); $i += 2) {
                        if (isset($rawFields[$i + 1])) {
                            $payload[$rawFields[$i]] = $rawFields[$i + 1];
                        }
                    }

                    try {
                        $eventType = $payload['event_type'] ?? null;

                        if ($eventType === 'order-updated' && isset($payload['payload'])) {
                            $updateData = json_decode($payload['payload'], true);

                            if (is_array($updateData)) {
                                $dto = $this->buildOrderUpdateDto($updateData);
                                $this->orderService->processOrderUpdate($dto);

                                $this->line("Processed Order update: order_id {$updateData['order_id']}, status: {$updateData['status']}");
                            }
                        }

                        $redis->executeRaw([
                            'XACK',
                            $stream,
                            $group,
                            $messageId,
                        ]);
                    } catch (Exception $e) {
                        Log::error("Failed to process order message ID {$messageId}: " . $e->getMessage());
                        $responseString = json_encode($response);
                        $this->error("Error processing message {$messageId}. Got from redis: {$responseString}. Check logs.");
                    }
                }
            }

            // Minimal sleep to avoid hard-loop CPU spiking when empty
            usleep(10000);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function buildOrderUpdateDto(array $data): OrderUpdateDTO
    {
        Log::info($data);
        return new OrderUpdateDTO(
            uuid:          (string) $data['order_id'],
            status:        (string) $data['status'],
            filledAmount:  (string) $data['filled_amount'],
        );
    }
}
