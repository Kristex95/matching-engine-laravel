<div class="flex flex-col items-center justify-center py-16 px-4 text-center bg-[#111214] border border-neutral-800 rounded-lg w-full">
    <div class="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
    </div>
    <h3 class="text-white font-semibold text-base mb-1">{{ $title }}</h3>
    <p class="text-neutral-500 text-xs max-w-xs mb-5">
        {{ $description }}
    </p>
    <a href="{{ route('login') }}" class="px-5 py-2 bg-blue-500 text-white font-semibold text-xs rounded hover:bg-blue-700 transition shadow-sm font-sans tracking-wide">
        Login to trade
    </a>
</div>