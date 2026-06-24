<?php

declare(strict_types=1);

namespace App\Modules\Orderbook\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

class OrderbookTracker extends Component
{
    public string $symbol;

    /**
     * @var array{bids: array<int|string, mixed>, asks: array<int|string, mixed>}
     */
    public array $orderbook = ['bids' => [], 'asks' => []];

    public function mount(string $symbol): void
    {
        $this->symbol = strtoupper($symbol);
    }

    public function fetchOrderbook(): void
    {
        /** @var string|null $data */
        $data = Redis::get("orderbook:{$this->symbol}");

        if ($data) {
            /** @var array{bids: array<int|string, mixed>, asks: array<int|string, mixed>} $decoded */
            $decoded = json_decode($data, true);
            $this->orderbook = $decoded;
        }
    }

    public function render(): View
    {
        $this->fetchOrderbook();

        return view('orderbook::livewire.orderbook-tracker');
    }
}
