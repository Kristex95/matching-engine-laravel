<div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow text-gray-900 dark:text-gray-100 w-full max-w-md">

    <div class="grid grid-cols-2 gap-2 mb-6 bg-gray-100 dark:bg-gray-900 p-1 rounded-md">
        <button type="button" wire:click="$set('side', 'buy')"
            class="py-2 text-center rounded font-semibold text-sm transition {{ $side === 'buy' ? 'bg-green-500 text-white' : 'text-gray-500' }}">
            Buy {{ $symbol }}
        </button>
        <button type="button" wire:click="$set('side', 'sell')"
            class="py-2 text-center rounded font-semibold text-sm transition {{ $side === 'sell' ? 'bg-red-500 text-white' : 'text-gray-500' }}">
            Sell {{ $symbol }}
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded dark:bg-green-900/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="placeOrder" class="space-y-4">
        <div>
            <label class="block text-xs uppercase font-bold text-gray-400 mb-1">Execution Mode</label>
            <select wire:model.live="type"
                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm">
                <option value="limit">Limit Order</option>
                <option value="market">Market Order</option>
            </select>
        </div>

        @if ($type === 'limit')
            <div>
                <label class="block text-xs uppercase font-bold text-gray-400 mb-1">Target Price (USDT)</label>
                <input type="number" step="any" wire:model="price" placeholder="0.00"
                    class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">
                @error('price')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>
        @endif

        <div>
            <label class="block text-xs uppercase font-bold text-gray-400 mb-1">Quantity ({{ $symbol }})</label>
            <input type="number" step="any" wire:model="amount" placeholder="0.00"
                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded p-2.5 text-sm focus:ring-blue-500 focus:border-blue-500">
            @error('amount')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        @if ($type === 'limit' && is_numeric($price) && is_numeric($amount))
            <div class="flex justify-between text-xs text-gray-400 pt-1">
                <span>Estimated Margin Hold:</span>
                <span class="font-bold text-gray-300">${{ number_format($price * $amount, 4) }} USDT</span>
            </div>
        @endif

        @auth
            <button type="submit"
                class="w-full mt-4 font-bold py-3 px-4 rounded text-white shadow transition-colors {{ $side === 'buy' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                Submit {{ ucfirst($side) }} Request
            </button>
        @else
            <button type="button" wire:click="redirectToLogin"
                class="w-full mt-4 font-bold py-3 px-4 rounded text-white shadow transition-colors bg-blue-600 hover:bg-blue-700">
                Login to trade
            </button>
        @endauth

    </form>
</div>
