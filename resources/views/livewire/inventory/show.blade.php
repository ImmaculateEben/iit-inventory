<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Inventory
            </a>
            <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $inventoryItem->item_name }}</h2>
            <p class="text-sm text-gray-500">{{ $inventoryItem->item_code }}</p>
        </div>
        @can('manage_inventory')
        <a href="{{ route('inventory.edit', $inventoryItem) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
            Edit
        </a>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- ============================================================== --}}
        {{-- Left column (2/3) --}}
        {{-- ============================================================== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Item Details --}}
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Item Details</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Type</dt><dd class="mt-1"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $inventoryItem->item_type->value === 'consumable' ? 'bg-emerald-50 text-emerald-700' : 'bg-violet-50 text-violet-700' }}">{{ ucfirst($inventoryItem->item_type->value) }}</span></dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Tracking</dt><dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inventoryItem->tracking_method->value) }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Category</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->category?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Department</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->department?->name ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Unit of Measure</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->unit_of_measure ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Size / Spec</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->size ?: '—' }}</dd></div>
                    @if($inventoryItem->manufacturer || $inventoryItem->model_number)
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Manufacturer</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->manufacturer ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Model Number</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->model_number ?: '—' }}</dd></div>
                    @endif
                    <div class="col-span-2"><dt class="text-xs font-medium text-gray-500 uppercase">Description</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->description ?: '—' }}</dd></div>
                </dl>
            </div>

            {{-- Procurement --}}
            @if($inventoryItem->supplier_donor || $inventoryItem->purchase_date || $inventoryItem->purchase_cost || $inventoryItem->warranty_info || $inventoryItem->guarantee_info)
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Procurement</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Supplier / Donor</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->supplier_donor ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Purchase Date</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->purchase_date?->format('M d, Y') ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Purchase Cost</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->purchase_cost ? 'KES ' . number_format($inventoryItem->purchase_cost, 2) : '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Warranty Info</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->warranty_info ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Warranty Expiry</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->warranty_expiry?->format('M d, Y') ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Guarantee Info</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->guarantee_info ?: '—' }}</dd></div>
                </dl>
            </div>
            @endif

            {{-- Location --}}
            @if($inventoryItem->floor || $inventoryItem->venue || $inventoryItem->venue_storage)
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Location</h3>
                <dl class="grid grid-cols-2 gap-4">
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Floor</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->floor ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Venue / Room</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->venue ?: '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase">Storage Area</dt><dd class="mt-1 text-sm text-gray-900">{{ $inventoryItem->venue_storage ?: '—' }}</dd></div>
                </dl>
            </div>
            @endif

            {{-- Asset Units --}}
            @if($inventoryItem->tracking_method->value === 'individual' && $inventoryItem->assetUnits->count())
            <div class="rounded-xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Asset Units ({{ $inventoryItem->assetUnits->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50"><tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Asset Tag</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Condition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Location</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($inventoryItem->assetUnits as $unit)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ $unit->asset_tag ?: '—' }}</td>
                                <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ $unit->serial_number ?: '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ ucfirst($unit->condition_status->value) }}</td>
                                <td class="px-6 py-3"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst(str_replace('_', ' ', $unit->unit_status->value)) }}</span></td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $unit->assigned_staff_name_snapshot ?: '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $unit->current_location ?: '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Custom Fields --}}
            @if($inventoryItem->customFieldValues->count())
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Custom Fields</h3>
                <dl class="grid grid-cols-2 gap-4">
                    @foreach($inventoryItem->customFieldValues as $cfv)
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase">{{ $cfv->customField?->label ?? 'Unknown' }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($cfv->value_boolean !== null)
                                    {{ $cfv->value_boolean ? 'Yes' : 'No' }}
                                @elseif($cfv->value_date)
                                    {{ \Carbon\Carbon::parse($cfv->value_date)->format('M d, Y') }}
                                @elseif($cfv->value_number !== null)
                                    {{ $cfv->value_number }}
                                @else
                                    {{ $cfv->value_text ?: '—' }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
            @endif

            {{-- Remarks --}}
            @if($inventoryItem->remarks)
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-2">Remarks</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $inventoryItem->remarks }}</p>
            </div>
            @endif

            {{-- Issues & Assignments --}}
            <div class="rounded-xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 px-6 py-4"><h3 class="text-base font-semibold text-gray-900">Issues & Assignments ({{ $inventoryItem->issueRecords->count() }})</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50"><tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Issued/Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">By</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($inventoryItem->issueRecords as $issue)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $issue->issued_at->format('M d, Y') }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ ($issue->action_type ?? 'issue') === 'assign' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700' }}">{{ ucfirst($issue->action_type ?? 'issue') }}</span>
                                </td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $issue->quantity }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $issue->staff_name_snapshot ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $issue->issuedBy?->name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No issue or assignment records yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Repair History --}}
            @if($inventoryItem->repairRecords->count())
            <div class="rounded-xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 px-6 py-4"><h3 class="text-base font-semibold text-gray-900">Repair History ({{ $inventoryItem->repairRecords->count() }})</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50"><tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Repair Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Component</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($inventoryItem->repairRecords as $repair)
                            <tr class="hover:bg-gray-50/50">
                                <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-600">{{ $repair->repair_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-3 text-sm font-medium text-gray-900">{{ $repair->component_repaired ?? '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-3">
                                    @php $rColors = ['reported'=>'bg-yellow-50 text-yellow-700','sent_for_repair'=>'bg-blue-50 text-blue-700','in_repair'=>'bg-indigo-50 text-indigo-700','repaired'=>'bg-green-50 text-green-700','returned'=>'bg-emerald-50 text-emerald-700','not_repairable'=>'bg-red-50 text-red-700']; @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $rColors[$repair->status->value ?? $repair->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('_', ' ', $repair->status->value ?? $repair->status)) }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-3 text-right text-sm">
                                    <a href="{{ route('repairs.show', $repair) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Stock Adjustments --}}
            <div class="rounded-xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 px-6 py-4"><h3 class="text-base font-semibold text-gray-900">Stock Adjustments ({{ $inventoryItem->stockAdjustments->count() }})</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50"><tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Delta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Note</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($inventoryItem->stockAdjustments as $adj)
                            <tr class="hover:bg-gray-50/50">
                                <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-600">{{ $adj->performed_at->format('M d, Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-3">
                                    @php $inc = $adj->delta_total > 0; @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $inc ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ $adj->action_type->label() }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-3 text-sm font-semibold {{ $inc ? 'text-green-600' : 'text-red-600' }}">{{ $inc ? '+' : '' }}{{ $adj->delta_total }}</td>
                                <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-600">{{ $adj->performedBy?->name ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $adj->note ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No stock adjustments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- Right sidebar (1/3) --}}
        {{-- ============================================================== --}}
        <div class="space-y-6">
            {{-- Stock Summary --}}
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Stock Summary</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Total / In Stock</span>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($inventoryItem->quantity_in_stock ?? $inventoryItem->quantity_total) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Available</span>
                        <span class="text-lg font-bold {{ ($inventoryItem->low_stock_threshold && $inventoryItem->quantity_available <= $inventoryItem->low_stock_threshold) ? 'text-amber-600' : 'text-emerald-600' }}">{{ number_format($inventoryItem->quantity_available) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Issued</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($inventoryItem->quantity_issued) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Damaged</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($inventoryItem->quantity_damaged) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Under Repair</span>
                        <span class="text-sm font-medium text-gray-900">{{ number_format($inventoryItem->quantity_under_repair) }}</span>
                    </div>
                    <hr>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Low Stock Threshold</span>
                        <span class="text-sm text-gray-700">{{ $inventoryItem->low_stock_threshold ?? 'System Default' }}</span>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Status</h3>
                @if($inventoryItem->is_active)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span> Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">
                        <span class="h-2 w-2 rounded-full bg-gray-400"></span> Inactive
                    </span>
                @endif
                <p class="mt-3 text-xs text-gray-400">Created {{ $inventoryItem->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-400">Updated {{ $inventoryItem->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>
