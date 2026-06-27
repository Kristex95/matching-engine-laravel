<div wire:poll.1s class="text-white font-mono h-full flex flex-col pl-2 py-2 justify-between">

    <div class="grid grid-cols-3 text-[10px] font-bold text-neutral-500 uppercase tracking-wider pb-2 pr-2 border-b border-neutral-800/60 flex-none select-none">
        <span class="text-left">Price(USDT)</span>
        <span class="text-center">Amount({{ strtoupper($symbol) }})</span>
        <span class="text-right">Time</span>
    </div>

    <div class="flex-1 min-h-0 mt-2 overflow-y-auto custom-scrollbar pr-1">
        <div class="space-y-1 w-full">
            @forelse($this->trades as $trade)
                <div class="grid grid-cols-3 text-xs w-full py-0.5">
                    <span class="text-left font-medium {{ ($trade['side'] ?? null) === 'buy' ? 'text-green-400' : 'text-red-400' }}">
                        {{ number_format((float)($trade['price'] ?? 0), 2) }}
                    </span>

                    <span class="text-center text-neutral-300">
                        {{ number_format((float)($trade['amount'] ?? 0), 4) }}
                    </span>

                    <span class="text-right text-neutral-400 text-[11px]">
                        {{ isset($trade['timestamp']) ? \Carbon\Carbon::createFromTimestampMs($trade['timestamp'])->format('H:i:s') : now()->format('H:i:s') }}
                    </span>
                </div>
            @empty
                <p class="text-xs text-neutral-500 italic text-center py-4">
                    Waiting for market executions...
                </p>
            @endforelse
        </div>
    </div>

</div>