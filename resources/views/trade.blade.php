<x-app-layout>
    <div class="py-6 min-h-screen text-gray-100">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-6">
            
            <div class="flex flex-col lg:flex-row gap-6 items-start">
                
                <div class="flex-1 w-full flex flex-col gap-6">
                    <div class="bg-neutral-900 border border-neutral-800 p-6 rounded-lg text-gray-100">
                        <h2 class="text-2xl font-bold mb-2">Trading ({{ strtoupper($currency) }} / USDT)</h2>
                        <p class="text-neutral-400 text-sm">
                            Specify execution parameters below to commit, lock balance allocations, and broadcast instructions directly to the matching engine core.
                        </p>
                    </div>

                    <livewire:orders::form :symbol="$currency" />
                </div>

                <div class="w-full lg:w-80 shrink-0 flex flex-col gap-6">
                    <livewire:orderbook::tracker :symbol="$currency" />
                    
                    <livewire:trades::recent-trades :symbol="$currency" />
                </div>
                
            </div>

            <div class="w-full border border-neutral-800 rounded-lg overflow-hidden bg-neutral-900">
                <livewire:shared::dashboard :symbol="$currency" />
            </div>

        </div>
    </div>
</x-app-layout>