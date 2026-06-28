<div class="">
    @guest
        <livewire:shared::auth-cta title="You are not authorized"
            description="Please sign in to establish a session verification context and track your live asset allocation parameters." />
    @endguest

    @auth
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-neutral-800 text-gray-500 font-medium tracking-wider">
                        <th class="py-2 px-4">Time</th>
                        <th class="py-2 px-4">Currency</th>
                        <th class="py-2 px-4">Side</th>
                        <th class="py-2 px-4">Price</th>
                        <th class="py-2 px-4">Amount</th>
                        <th class="py-2 px-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800/40">
                    @forelse($trades as $trade)
                        @php
                            $isTaker = $trade->taker_account_id === auth()->user()->account_id;
                            
                            $userSide = $isTaker ? 'Taker' : 'Maker';
                        @endphp
                        <tr class="hover:bg-neutral-800/20 transition">
                            <td class="py-3 px-4 text-gray-500">
                                {{ $trade->created_at?->format('H:i:s') ?? '—' }}
                            </td>

                            <td class="py-3 px-4 font-mono text-gray-300">
                                {{ $trade->currency }}
                            </td>
                            
                            <td class="py-3 px-4 uppercase font-bold text-gray-300">
                                <span class="text-xs text-gray-500 font-normal">({{ $userSide }})</span>
                            </td>

                            <td class="py-3 px-4 font-mono text-gray-300">
                                {{ number_format((float) $trade->price, 2) }}
                            </td>

                            <td class="py-3 px-4 font-mono text-gray-300">
                                {{ $trade->amount }}
                            </td>

                            <td class="py-3 px-4 font-mono text-gray-300 text-right">
                                {{ number_format((float) $trade->price * (float) $trade->amount, 4) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-600">
                                No trades found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($trades->hasPages())
            <div class="mt-4 pt-4 border-t border-neutral-800 text-gray-400">
                {{ $trades->links() }}
            </div>
        @endif
    @endauth
</div>