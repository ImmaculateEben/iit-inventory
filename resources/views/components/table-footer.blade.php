@props(['paginator'])

<div class="border-t border-gray-100 px-6 py-4">
    <div class="flex items-center justify-between">
        {{-- Per-page selector --}}
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Show</span>
            <select wire:model.live="perPage" class="rounded-md border-gray-300 py-1.5 pl-3 pr-8 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
            </select>
            <span>entries</span>
        </div>

        {{-- Pagination links --}}
        @if($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator && $paginator->hasPages())
            <div>{{ $paginator->links() }}</div>
        @endif
    </div>
</div>
