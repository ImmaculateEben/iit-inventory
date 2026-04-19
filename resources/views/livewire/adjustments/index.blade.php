<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h2 class="text-2xl font-bold text-gray-900">Stock Adjustments</h2><p class="mt-1 text-sm text-gray-500">Track all stock adjustments</p></div>
        <a href="{{ route('adjustments.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>New Adjustment</a>
    </div>
    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
        <div class="relative sm:max-w-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by item name..." class="w-full pl-10">
        </div>
    </div>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100">
        <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50"><tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Adj. No.</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Delta</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">By</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($adjustments as $adj)
                    <tr class="hover:bg-gray-50/50"
                        x-data="{ showNote: false, pos: { top: 0, left: 0 } }"
                        @mouseenter="
                            let rect = $el.getBoundingClientRect();
                            pos = { top: rect.top - 8, left: rect.left + rect.width / 2 };
                            showNote = true;
                        "
                        @mouseleave="showNote = false">
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">{{ $adj->performed_at->format('M d, Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm font-mono text-gray-500">{{ $adj->adjustment_number }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">{{ $adj->inventoryItem?->item_name }}</td>
                        <td class="whitespace-nowrap px-4 py-4">
                            @php $inc = $adj->delta_total > 0; @endphp
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $inc ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ $adj->action_type->label() }}</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold {{ $inc ? 'text-green-600' : 'text-red-600' }}">{{ $inc ? '+' : '' }}{{ $adj->delta_total }}</td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">{{ $adj->performedBy?->name }}</td>
                        @if($adj->note)
                        <template x-teleport="body">
                            <div x-show="showNote" x-transition.opacity.duration.150ms x-cloak
                                 :style="`position:fixed; top:${pos.top}px; left:${pos.left}px; transform:translate(-50%,-100%); z-index:9999; width:18rem; border-radius:0.5rem; background:#111827; padding:0.625rem 1rem; font-size:0.75rem; line-height:1.625; color:#fff; box-shadow:0 10px 15px -3px rgba(0,0,0,.3);`">
                                <span style="display:block; font-weight:600; color:#9ca3af; margin-bottom:0.25rem;">Note</span>
                                {{ $adj->note }}
                                <div style="position:absolute; left:50%; top:100%; transform:translateX(-50%); width:0; height:0; border-left:6px solid transparent; border-right:6px solid transparent; border-top:6px solid #111827;"></div>
                            </div>
                        </template>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">No adjustments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @if($adjustments->hasPages())<div class="border-t border-gray-100 px-4 py-4">{{ $adjustments->links() }}</div>@endif
    </div>
</div>
