<x-app-layout>
    <div class="h-[calc(100vh-64px)] text-gray-200 bg-neutral-950 font-sans select-none overflow-y-auto custom-scrollbar">
        
        <div class="w-full h-full grid grid-cols-1 lg:grid-cols-12 bg-neutral-800 gap-[1px] border-b border-neutral-800">
            
            <div class="lg:col-span-9 xl:col-span-10 flex flex-col h-full bg-neutral-900 min-h-0">
                
                <div class="h-[calc(100vh-300px)] flex-none grid grid-cols-1 lg:grid-cols-4 xl:grid-cols-5 min-h-0 bg-neutral-800 gap-[1px] border-b border-neutral-800">
                    
                    <div class="lg:col-span-3 xl:col-span-4 flex flex-col bg-neutral-900 min-h-0">
                        <livewire:trading.crypto-chart :symbol="$currency" />
                    </div>
                    
                    <div class="lg:col-span-1 grid grid-rows-2 h-full bg-neutral-950 gap-[1px] min-h-0">
                        <div class="bg-neutral-900 overflow-y-auto p-3 custom-scrollbar">
                            <livewire:orderbook::tracker :symbol="$currency" />
                        </div>
                        <div class="bg-neutral-900 overflow-y-auto p-3 custom-scrollbar">
                            <livewire:trades::recent-trades :symbol="$currency" />
                        </div>
                    </div>
                </div>

                <div class="flex-1 bg-neutral-900">
                    <livewire:shared::dashboard :symbol="$currency" />
                </div>
            </div>

            <div class="lg:col-span-3 xl:col-span-2 h-full bg-neutral-900 overflow-y-auto custom-scrollbar">
                <livewire:orders::form :symbol="$currency" />
            </div>

        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #171717;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #404040;
            border-radius: 2px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #525252;
        }
    </style>
</x-app-layout>