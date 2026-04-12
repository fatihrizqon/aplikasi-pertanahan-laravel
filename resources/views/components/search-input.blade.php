<div class="relative font-normal">
    <form method="GET" action="{{ $route }}" class="relative">

        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none px-2">
            <i data-lucide="search" class="w-5 h-5 size-4 text-gray-400 dark:text-white/60"></i>
        </div>

        <input name="search" class="py-2 ps-8 pe-4 block w-full border-gray-200 rounded-lg sm:text-xs
                   focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900
                   dark:border-neutral-700 dark:text-neutral-400" type="text" placeholder="Search..." value="{{ request('search') }}">

        <x-preserve-query :except="['search', 'page']" />
    </form>
</div>
