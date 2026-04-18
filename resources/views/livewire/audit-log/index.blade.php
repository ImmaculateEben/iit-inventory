<div>
    <div class="mb-6"><h2 class="text-2xl font-bold text-gray-900">Audit Log</h2><p class="mt-1 text-sm text-gray-500">Complete history of system actions</p></div>
    <div class="mb-6 rounded-xl bg-white p-5 shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search actions or users..." class="w-full pl-10">
            </div>
            <select wire:model.live="filterAction" class="w-full">
                <option value="">All Actions</option>
                @foreach($actions as $action)<option value="{{ $action }}">{{ $action }}</option>@endforeach
            </select>
        </div>
    </div>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Entity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">IP Address</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="whitespace-nowrap px-6 py-4"><span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $log->action_code }}</span></td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            @if($log->target_type){{ class_basename($log->target_type) }} #{{ $log->target_id }}@else — @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-mono text-gray-400">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $logs->links() }}</div>@endif
    </div>
</div>
