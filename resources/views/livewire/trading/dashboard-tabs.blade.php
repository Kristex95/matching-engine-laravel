<div class="w-full text-gray-300">
    <div class="flex items-center border-b border-neutral-800 bg-neutral-900 px-4 text-sm font-medium overflow-x-auto">

        <button wire:click="selectTab('open-orders')"
            class="flex items-center gap-2 py-3 px-4 border-b-2 transition {{ $activeTab === 'open-orders' ? 'border-blue-500 text-white bg-neutral-800/50' : 'border-transparent text-gray-400 hover:text-white' }}">
            <span>Open orders</span>
        </button>

        <button wire:click="selectTab('history-orders')"
            class="flex items-center py-3 px-4 border-b-2 transition {{ $activeTab === 'history-orders' ? 'border-blue-500 text-white bg-neutral-800/50' : 'border-transparent text-gray-400 hover:text-white' }}">
            Order history
        </button>

        <button wire:click="selectTab('trade-history')"
            class="flex items-center py-3 px-4 border-b-2 transition {{ $activeTab === 'trade-history' ? 'border-blue-500 text-white bg-neutral-800/50' : 'border-transparent text-gray-400 hover:text-white' }}">
            Trade history
        </button>

        <button wire:click="selectTab('balances')"
            class="flex items-center py-3 px-4 border-b-2 transition {{ $activeTab === 'balances' ? 'border-blue-500 text-white bg-neutral-800/50' : 'border-transparent text-gray-400 hover:text-white' }}">
            Balances
        </button>
    </div>

    <div class="p-4 bg-neutral-950">
        @if ($activeTab === 'open-orders')
            @livewire('trading.open-orders', key('tab-open-orders'))
        @elseif($activeTab === 'history-orders')
            @livewire('trading.history-orders', key('tab-order-history'))
        @elseif($activeTab === 'trade-history')
            {{-- Analogous for other tabs... --}}
        @elseif($activeTab === 'balances')
            @livewire('trading.balances', ['symbol' => $symbol ?? 'BTC'], key('tab-balances'))
        @endif
    </div>
</div>
