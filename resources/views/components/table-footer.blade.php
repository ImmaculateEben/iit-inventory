@props(['paginator'])

<div class="border-t border-gray-100 px-6 py-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Per-page selector + showing entries --}}
        <div class="flex items-center gap-4 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <span>Show</span>
                <select wire:model.live="perPage" class="rounded-md border-gray-300 py-1.5 pl-3 pr-8 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
            <span class="hidden sm:inline text-gray-400">|</span>
            <span class="text-gray-500">
                Showing {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }}
            </span>
        </div>

        {{-- Pagination links --}}
        @if($paginator->hasPages())
            <div>{{ $paginator->links() }}</div>
        @endif
    </div>
</div>
