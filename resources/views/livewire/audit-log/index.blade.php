<div>
    @php
        $actionLabels = [
            'user.login' => ['label' => 'Logged In', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => '→'],
            'user.logout' => ['label' => 'Logged Out', 'bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'icon' => '←'],
            'login_failed' => ['label' => 'Failed Login', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => '✕'],
            'user_created' => ['label' => 'User Created', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => '+'],
            'user_updated' => ['label' => 'User Updated', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => '✎'],
            'role_created' => ['label' => 'Role Created', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => '+'],
            'role_updated' => ['label' => 'Role Updated', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'icon' => '✎'],
            'inventory_item_created' => ['label' => 'Item Added', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => '+'],
            'inventory_item_updated' => ['label' => 'Item Updated', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => '✎'],
            'item_issued' => ['label' => 'Item Issued', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'icon' => '↗'],
            'item_assigned' => ['label' => 'Item Assigned', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'icon' => '→'],
            'item_returned' => ['label' => 'Item Returned', 'bg' => 'bg-teal-100', 'text' => 'text-teal-700', 'icon' => '↩'],
            'stock_adjusted' => ['label' => 'Stock Adjusted', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => '±'],
            'repair_created' => ['label' => 'Repair Logged', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => '+'],
            'repair_updated' => ['label' => 'Repair Updated', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'icon' => '✎'],
            'repair_status_updated' => ['label' => 'Repair Status Changed', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => '↻'],
            'department_updated' => ['label' => 'Department Updated', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'icon' => '✎'],
            'category_created' => ['label' => 'Category Created', 'bg' => 'bg-violet-100', 'text' => 'text-violet-700', 'icon' => '+'],
            'category_updated' => ['label' => 'Category Updated', 'bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'icon' => '✎'],
        ];

        $entityLabels = [
            'App\\Models\\User' => 'User',
            'App\\Models\\InventoryItem' => 'Inventory Item',
            'App\\Models\\IssueRecord' => 'Issue Record',
            'App\\Models\\ReturnRecord' => 'Return Record',
            'App\\Models\\RepairRecord' => 'Repair Record',
            'App\\Models\\StockAdjustment' => 'Stock Adjustment',
            'App\\Models\\Role' => 'Role',
            'App\\Models\\Department' => 'Department',
            'App\\Models\\Category' => 'Category',
            'user' => 'User',
        ];

        $defaultAction = ['label' => null, 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => '•'];
    @endphp

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
                @foreach($actions as $action)<option value="{{ $action }}">{{ $actionLabels[$action]['label'] ?? ucwords(str_replace(['_', '.'], ' ', $action)) }}</option>@endforeach
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
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Details</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">IP Address</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    @php
                        $a = $actionLabels[$log->action_code] ?? $defaultAction;
                        $friendlyAction = $a['label'] ?? ucwords(str_replace(['_', '.'], ' ', $log->action_code));
                        $friendlyEntity = $entityLabels[$log->target_type] ?? ($log->target_type ? class_basename($log->target_type) : null);
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex items-center gap-1 rounded-full {{ $a['bg'] }} px-2.5 py-0.5 text-xs font-medium {{ $a['text'] }}">
                                <span class="text-[10px]">{{ $a['icon'] }}</span>
                                {{ $friendlyAction }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            @if($friendlyEntity && $log->target_id)
                                {{ $friendlyEntity }} <span class="text-gray-400">#{{ $log->target_id }}</span>
                            @elseif($friendlyEntity)
                                {{ $friendlyEntity }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-mono text-gray-400">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-table-footer :paginator="$logs" />
    </div>
</div>
