<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Repair Records</h2>
            <p class="mt-1 text-sm text-gray-500">Track and manage item repairs</p>
        </div>
        @can('manage_repairs')
        <a href="{{ route('repairs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Report Repair
        </a>
        @endcan
    </div>

    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by item name..." class="w-full pl-10">
            </div>
            <select wire:model.live="filterStatus">
                <option value="">All Statuses</option>
                <option value="reported">Reported</option>
                <option value="sent_for_repair">Sent for Repair</option>
                <option value="in_repair">In Repair</option>
                <option value="repaired">Repaired</option>
                <option value="returned">Returned</option>
                <option value="not_repairable">Not Repairable</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Component</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Repair Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Reported By</th>
                    <th class="px-6 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($repairs as $repair)
                    <tr class="hover:bg-gray-50/50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $repair->inventoryItem?->item_name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $repair->component_repaired ?? '—' }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $repair->repair_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @php $rColors = ['reported'=>'bg-yellow-50 text-yellow-700','sent_for_repair'=>'bg-blue-50 text-blue-700','in_repair'=>'bg-indigo-50 text-indigo-700','repaired'=>'bg-green-50 text-green-700','returned'=>'bg-emerald-50 text-emerald-700','not_repairable'=>'bg-red-50 text-red-700']; @endphp
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $rColors[$repair->status->value ?? $repair->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('_', ' ', $repair->status->value ?? $repair->status)) }}</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $repair->createdBy?->name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                            <a href="{{ route('repairs.show', $repair) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No repair records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($repairs->hasPages())
        <div class="border-t border-gray-100 px-6 py-4">{{ $repairs->links() }}</div>
        @endif
    </div>
</div>
