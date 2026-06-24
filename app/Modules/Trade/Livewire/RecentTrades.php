<?php

declare(strict_types=1);

namespace App\Modules\Trade\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Recent Market Trades')]
class RecentTrades extends Component
{
    public string $symbol;

    /**
     * Computed property/method to pull the 20 latest trades from Redis.
     *
     * @return array<int, mixed>
     */
    public function getTradesProperty(): array
    {
        $base = strtoupper($this->symbol);
        $cacheKey = "trades:recent:{$base}";

        /** @var \Illuminate\Redis\Connections\Connection|\Redis $redis */
        $redis = Redis::connection();

        // Using lrange natively is typically better supported by static analysis
        $rawTrades = $redis->lrange($cacheKey, 0, 19);

        return array_map(fn ($trade) => json_decode((string) $trade, true), $rawTrades);
    }

    public function render(): View
    {
        return view('trades::livewire.recent-trades');
    }
}
