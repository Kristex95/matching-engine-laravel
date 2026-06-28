<div>
    @if($message)
        <div 
            x-data="{ show: true }" 
            x-show="show"
            x-init="setTimeout(() => { show = false; $wire.clearBanner(); }, 5000)"
            class="fixed top-4 right-4 z-50 bg-blue-600 text-white px-4 py-3 rounded shadow-lg flex items-center justify-between min-w-[300px]"
            x-transition
        >
            <span>{{ $message }}</span>
            <button @click="show = false; $wire.clearBanner()" class="ml-4 font-bold text-white hover:text-gray-200">
                &times;
            </button>
        </div>
    @endif
</div>