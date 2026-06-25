<div wire:poll.1s="fetchOrderbook" class="text-white font-mono h-full flex pl-2 py-2 flex-col justify-between">
    
    <div class="grid grid-cols-3 text-[10px] font-bold text-neutral-500 uppercase tracking-wider pb-2 pr-2 border-b border-neutral-800/60 flex-none select-none">
        <span class="text-left">Price</span>
        <span class="text-center">Amount</span>
        <span class="text-right">Total</span>
    </div>

    <div class="flex-1 flex flex-col min-h-0 mb-2 mt-2">
        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 flex flex-col justify-end">
            <div class="space-y-1 w-full">
                @forelse(array_reverse($orderbook['asks'] ?? []) as $ask)
                    <div class="grid grid-cols-3 text-red-400 text-xs w-full">
                        <span class="text-left">{{ number_format($ask[0], 2) }}</span>
                        <span class="text-center text-neutral-300">{{ $ask[1] }}</span>
                        <span class="text-right text-neutral-400">{{ number_format($ask[0] * $ask[1], 2) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-neutral-500 italic text-center py-2">No ask orders</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="border-t border-neutral-800 my-1 flex-none"></div>

    <div class="flex-1 flex flex-col min-h-0 mt-2">
        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 space-y-1">
            @forelse($orderbook['bids'] ?? [] as $bid)
                <div class="grid grid-cols-3 text-green-400 text-xs w-full">
                    <span class="text-left">{{ number_format($bid[0], 2) }}</span>
                    <span class="text-center text-neutral-300">{{ $bid[1] }}</span>
                    <span class="text-right text-neutral-400">{{ number_format($bid[0] * $bid[1], 2) }}</span>
                </div>
            @empty
                <p class="text-xs text-neutral-500 italic text-center py-2">No bid orders</p>
            @endforelse
        </div>
    </div>

</div>