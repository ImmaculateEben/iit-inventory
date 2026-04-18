<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Issues & Assignments</h2>
            <p class="mt-1 text-sm text-gray-500">Track all items issued or assigned to staff</p>
        </div>
        <a href="{{ route('issues.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Issue / Assign
        </a>
    </div>

    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by item name..." class="w-full pl-10">
            </div>
            <select wire:model.live="filterType">
                <option value="">All Types</option>
                <option value="issue">Issues</option>
                <option value="assign">Assignments</option>
            </select>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Issued/Assigned To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Notes</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($issues as $issue)
                    <tr class="hover:bg-gray-50/50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $issue->issued_at->format('M d, Y') }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ ($issue->action_type ?? 'issue') === 'assign' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">{{ ucfirst($issue->action_type ?? 'issue') }}</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $issue->inventoryItem?->item_name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">{{ $issue->quantity }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $issue->staff_name_snapshot ?? '—' }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $issue->issuedBy?->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $issue->note ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($issues->hasPages())
        <div class="border-t border-gray-100 px-6 py-4">{{ $issues->links() }}</div>
        @endif
    </div>
</div>
