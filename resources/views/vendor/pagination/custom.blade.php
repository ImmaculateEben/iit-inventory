@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center rounded-md px-3 py-1.5 text-sm text-gray-400 cursor-default">Previous</span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled" class="inline-flex items-center rounded-md px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 transition">Previous</button>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex items-center rounded-md px-3 py-1.5 text-sm text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="inline-flex items-center rounded-md bg-blue-100 px-3 py-1.5 text-sm font-medium text-blue-700">{{ $page }}</span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" class="inline-flex items-center rounded-md px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 transition">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled" class="inline-flex items-center rounded-md px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 transition">Next</button>
        @else
            <span class="inline-flex items-center rounded-md px-3 py-1.5 text-sm text-gray-400 cursor-default">Next</span>
        @endif
    </nav>
@endif
