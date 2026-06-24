<div wire:poll.1s="fetchOrderbook" class="bg-gray-900 text-white p-6 rounded-lg shadow-lg font-mono w-80">
    <h3 class="text-lg font-bold mb-4 border-b border-gray-700 pb-2">
        Orderbook: {{ $orderbook['symbol'] ?? $symbol . 'USDT' }}
    </h3>

    <div class="mb-4">
        <h4 class="text-xs text-gray-400 uppercase mb-1">Asks (Sell)</h4>
        <div class="space-y-1">
            @forelse(array_reverse($orderbook['asks'] ?? []) as $ask)
                <div class="flex justify-between text-red-400 text-sm">
                    <span>${{ number_format($ask[0], 2) }}</span>
                    <span>{{ $ask[1] }}</span>
                </div>
            @empty
                <p class="text-xs text-gray-500">No ask orders</p>
            @endforelse
        </div>
    </div>

    <div class="border-t border-gray-800 my-2"></div>

    <div>
        <h4 class="text-xs text-gray-400 uppercase mb-1">Bids (Buy)</h4>
        <div class="space-y-1">
            @forelse($orderbook['bids'] ?? [] as $bid)
                <div class="flex justify-between text-green-400 text-sm">
                    <span>${{ number_format($bid[0], 2) }}</span>
                    <span>{{ $bid[1] }}</span>
                </div>
            @empty
                <p class="text-xs text-gray-500">No bid orders</p>
            @endforelse
        </div>
    </div>
</div>