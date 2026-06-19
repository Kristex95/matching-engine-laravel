<?php

declare(strict_types=1);

namespace App\Modules\Trade\Console\Commands;

use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Application\Services\TradeService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ConsumeTradesStream extends Command
{
    protected $signature = 'stream:consume-trades';

    protected $description = 'Consume incoming trades from the Redis trades-stream';

    public function __construct(
        private TradeService $tradeService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $stream = 'trades-stream';
        $group = 'laravel-workers';
        $consumer = 'laravel-consumer-1';

        /** @var \Illuminate\Redis\Connections\PhpRedisConnection $redis */
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

                        if ($eventType === 'trade-executed' && isset($payload['payload'])) {
                            $tradeData = json_decode($payload['payload'], true);

                            if ($tradeData) {
                                $dto = $this->buildTradeDto($tradeData);
                                $this->tradeService->processNewTrade($dto);

                                $this->line("Processed Trade: Taker {$tradeData['taker_order_id']} matched with Maker {$tradeData['maker_order_id']}");
                            }
                        }

                        $redis->executeRaw([
                            'XACK',
                            $stream,
                            $group,
                            $messageId,
                        ]);
                    } catch (Exception $e) {
                        Log::error("Failed to process trade message ID {$messageId}: " . $e->getMessage());
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
    private function buildTradeDto(array $data): StoreTradeDTO
    {
        Log::info($data);
        return new StoreTradeDTO(
            takerOrderId:  (string) $data['taker_order_id'],
            makerOrderId:  (string) $data['maker_order_id'],
            price:         (string) $data['price'],
            amount:        (string) $data['amount'],
            quoteCurrency: (string) $data['quote_currency'],
            baseCurrency:  (string) $data['base_currency'],
        );
    }
}
