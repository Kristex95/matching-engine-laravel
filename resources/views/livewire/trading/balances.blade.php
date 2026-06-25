<div class="border-neutral-800 rounded-lg text-gray-400">
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
                        <th class="py-2 px-4">Asset</th>
                        <th class="py-2 px-4">Total Balance</th>
                        <th class="py-2 px-4">Available Allocation</th>
                        <th class="py-2 px-4">In Orders (Locked)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-800/40 font-mono">
                    @forelse($balances as $balance)
                        <tr class="hover:bg-neutral-800/20 transition {{ $balance->currency === strtoupper($symbol) ? 'bg-amber-500/5' : '' }}">
                            <td class="py-3 px-4 text-gray-200 font-bold tracking-wide font-sans">
                                {{ $balance->currency }}
                                @if($balance->currency === strtoupper($symbol))
                                    <span class="ml-1 text-[10px] text-amber-500 font-normal border border-amber-500/30 px-1 py-0.2 rounded">Active Market</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-300">
                                {{ number_format((float)bcadd($balance->available, $balance->locked, 8), 8) }}
                            </td>
                            <td class="py-3 px-4 text-green-400">
                                {{ number_format((float)$balance->available, 8) }}
                            </td>
                            <td class="py-3 px-4 text-gray-500">
                                {{ number_format((float)$balance->locked, 8) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-600">
                                No balance records mapped to this session profile.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endauth
</div>