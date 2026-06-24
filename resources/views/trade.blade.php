<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
            
            <div class="flex-1 flex flex-col gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow text-gray-900 dark:text-gray-100">
                    <h2 class="text-2xl font-bold mb-4">Trading ({{ strtoupper($currency) }} / USDT)</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        Specify execution parameters below to commit, lock balance allocations, and broadcast instructions directly to the matching engine core.
                    </p>
                </div>

                <livewire:orders::form :symbol="$currency" />
            </div>

            <div class="w-full md:w-80">
                <livewire:orderbook::tracker :symbol="$currency" />
            </div>

        </div>
    </div>
</x-app-layout>