<div wire:poll.1s class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow text-gray-900 dark:text-gray-100">
    <h3 class="text-lg font-bold mb-4">Recent Market Trades</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-gray-500 border-b border-gray-200 dark:border-gray-700">
                    <th class="pb-2">Price (USDT)</th>
                    <th class="pb-2">Amount ({{ strtoupper($symbol) }})</th>
                    <th class="pb-2 text-right">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($this->trades as $trade)
                    @php
                        // Determine if it was a buy or sell execution if your data provides it
                        $isMakerTakerMatch = !empty($trade['maker_order_id']);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="py-2 font-mono font-medium {{ ($trade['side'] ?? null) === 'buy' ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ number_format((float)($trade['price'] ?? 0), 2) }}
                        </td>
                        <td class="py-2 font-mono">
                            {{ number_format((float)($trade['amount'] ?? 0), 4) }}
                        </td>
                        <td class="py-2 font-mono text-xs text-gray-400 text-right">
                            {{-- Displays trade timestamp or current system time --}}
                            {{ isset($trade['timestamp']) ? \Carbon\Carbon::createFromTimestampMs($trade['timestamp'])->format('H:i:s') : now()->format('H:i:s') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500 text-xs">
                            Waiting for market executions...
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>