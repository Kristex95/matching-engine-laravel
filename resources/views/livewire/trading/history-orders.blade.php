<div class="">
    @guest
        <livewire:shared::auth-cta 
            title="You are not authorized" 
            description="Please sign in to establish a session verification context and track your live asset allocation parameters." 
        />
    @endguest

    @auth
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-neutral-800 text-gray-500 font-medium tracking-wider">
                        <th class="py-2 px-4">Time</th>
                        <th class="py-2 px-4">Type</th>
                        <th class="py-2 px-4">Side</th>
                        <th class="py-2 px-4">Price</th>
                        <th class="py-2 px-4">Amount</th>
                        <th class="py-2 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800/40">
                    @forelse($orders as $order)
                        <tr class="hover:bg-neutral-800/20 transition">
                            <td class="py-3 px-4 text-gray-500">{{ $order->created_at?->format('H:i:s') ?? '—' }}</td>
                            <td class="py-3 px-4 uppercase font-semibold text-gray-300">{{ $order->type }}</td>
                            <td class="py-3 px-4 uppercase font-bold {{ $order->side === 'buy' ? 'text-green-500' : 'text-red-500' }}">
                                {{ $order->side }}
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-300">{{ number_format((float)$order->price, 2) }}</td>
                            <td class="py-3 px-4 font-mono text-gray-300">{{ $order->amount }}</td>
                            <td class="py-3 px-4 font-mono text-gray-500">{{ $order->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-600">
                                No open orders execution parameters found for {{ strtoupper($symbol) }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="mt-4 pt-4 border-t border-neutral-800 text-gray-400">
                {{ $orders->links() }}
            </div>
        @endif
    @endauth
</div>