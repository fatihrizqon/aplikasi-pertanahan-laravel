@if ($paginator->hasPages())
<nav class="flex items-center gap-x-1 mt-4" aria-label="Pagination">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
    <span class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center text-sm rounded-lg text-gray-400 cursor-not-allowed dark:text-neutral-600" aria-hidden="true">
        <svg class="shrink-0 size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center text-sm rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-white/10" aria-label="Previous">
        <svg class="shrink-0 size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>
    @endif

    {{-- Pagination Elements --}}
    <div class="flex items-center gap-x-1">
        @foreach ($elements as $element)
        @if (is_string($element))
        <span class="px-3 text-sm text-gray-400 dark:text-neutral-600">{{ $element }}</span>
        @endif

        @if (is_array($element))
        @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-300 text-white bg-gray-800 dark:bg-white dark:text-black py-2 px-3 text-sm font-normal rounded-lg">
            {{ $page }}
        </span>
        @else
        <a href="{{ $url }}" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-transparent text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-white/10 py-2 px-3 text-sm font-normal rounded-lg">
            {{ $page }}
        </a>
        @endif
        @endforeach
        @endif
        @endforeach
    </div>

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center text-sm rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-white/10" aria-label="Next">
        <svg class="shrink-0 size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>
    @else
    <span class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center text-sm rounded-lg text-gray-400 cursor-not-allowed dark:text-neutral-600" aria-hidden="true">
        <svg class="shrink-0 size-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 18l6-6-6-6" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </span>
    @endif
</nav>
@endif