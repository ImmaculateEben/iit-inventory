<div>
    <div class="mb-6">
        <a href="{{ route('repairs.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Repairs
        </a>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Repair: {{ $repairRecord->inventoryItem?->item_name }}</h2>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Repair Details</h3>
            <dl class="grid grid-cols-2 gap-4">
                <div><dt class="text-xs font-medium text-gray-500 uppercase">Item</dt><dd class="mt-1 text-sm text-gray-900">{{ $repairRecord->inventoryItem?->item_name }}</dd></div>
                <div><dt class="text-xs font-medium text-gray-500 uppercase">Reported By</dt><dd class="mt-1 text-sm text-gray-900">{{ $repairRecord->createdBy?->name }}</dd></div>
                <div><dt class="text-xs font-medium text-gray-500 uppercase">Component Repaired</dt><dd class="mt-1 text-sm text-gray-900">{{ $repairRecord->component_repaired ?? '—' }}</dd></div>
                <div><dt class="text-xs font-medium text-gray-500 uppercase">Repair Date</dt><dd class="mt-1 text-sm text-gray-900">{{ $repairRecord->repair_date?->format('M d, Y') ?? '—' }}</dd></div>
                <div class="col-span-2"><dt class="text-xs font-medium text-gray-500 uppercase">Repair Description</dt><dd class="mt-1 text-sm text-gray-900">{{ $repairRecord->repair_description ?? $repairRecord->problem_description }}</dd></div>
                @if($repairRecord->repair_notes)
                <div class="col-span-2"><dt class="text-xs font-medium text-gray-500 uppercase">Repair Notes</dt><dd class="mt-1 text-sm text-gray-900">{{ $repairRecord->repair_notes }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 mb-3">Status</h3>
                @php $rColors = ['reported'=>'bg-yellow-50 text-yellow-700','sent_for_repair'=>'bg-blue-50 text-blue-700','in_repair'=>'bg-indigo-50 text-indigo-700','repaired'=>'bg-green-50 text-green-700','returned'=>'bg-emerald-50 text-emerald-700','not_repairable'=>'bg-red-50 text-red-700']; @endphp
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium {{ $rColors[$repairRecord->status->value ?? $repairRecord->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('_', ' ', $repairRecord->status->value ?? $repairRecord->status)) }}</span>

                @can('manage_repairs')
                <div class="mt-4 space-y-2">
                    <p class="text-xs font-medium text-gray-500 uppercase">Update Status</p>
                    @foreach(['reported','sent_for_repair','in_repair','repaired','returned','not_repairable'] as $s)
                        @if($s !== $repairRecord->status)
                        <button wire:click="updateStatus('{{ $s }}')" class="block w-full text-left rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </button>
                        @endif
                    @endforeach
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
