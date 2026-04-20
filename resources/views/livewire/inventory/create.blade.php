<div>
    <div class="mb-6">
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Inventory
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Add Inventory Item</h1>
    </div>

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <form wire:submit="save" class="space-y-8">

        {{-- ============================================================== --}}
        {{-- SECTION 1: Basic Information --}}
        {{-- ============================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Item Code --}}
                <div>
                    <label for="item_code" class="block text-sm font-medium text-gray-700 mb-1">Item Code <span class="text-red-500">*</span></label>
                    <input type="text" id="item_code" wire:model="item_code" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. IIT-LAP-001">
                    @if($item_code && !$codeManuallyEdited)
                        <p class="mt-1 text-xs text-blue-500">Auto-generated &mdash; you can edit this</p>
                    @endif
                    @error('item_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Item Name --}}
                <div>
                    <label for="item_name" class="block text-sm font-medium text-gray-700 mb-1">Item Name <span class="text-red-500">*</span></label>
                    <input type="text" id="item_name" wire:model="item_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Dell Latitude 5540">
                    @error('item_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Unit of Measure --}}
                <div>
                    <label for="unit_of_measure" class="block text-sm font-medium text-gray-700 mb-1">Unit of Measure</label>
                    <input type="text" id="unit_of_measure" wire:model="unit_of_measure" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Pieces, Litres, Reams">
                    @error('unit_of_measure') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Item Type --}}
                <div>
                    <label for="item_type" class="block text-sm font-medium text-gray-700 mb-1">Item Type <span class="text-red-500">*</span></label>
                    <select id="item_type" wire:model.live="item_type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @foreach(\App\Enums\ItemType::cases() as $type)
                            <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                        @endforeach
                    </select>
                    @error('item_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tracking Method --}}
                <div>
                    <label for="tracking_method" class="block text-sm font-medium text-gray-700 mb-1">Tracking Method <span class="text-red-500">*</span></label>
                    <select id="tracking_method" wire:model.live="tracking_method" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @foreach(\App\Enums\TrackingMethod::cases() as $method)
                            <option value="{{ $method->value }}">{{ ucfirst($method->value) }}</option>
                        @endforeach
                    </select>
                    @error('tracking_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select id="category_id" wire:model="category_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">— Select Category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Department --}}
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-500">*</span></label>
                    <select id="department_id" wire:model="department_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">— Select Department —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Manufacturer --}}
                <div>
                    <label for="manufacturer" class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                    <input type="text" id="manufacturer" wire:model="manufacturer" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Dell, HP, Samsung">
                    @error('manufacturer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Model Number --}}
                <div>
                    <label for="model_number" class="block text-sm font-medium text-gray-700 mb-1">Model Number</label>
                    <input type="text" id="model_number" wire:model="model_number" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. LAT-5540-i7">
                    @error('model_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- SECTION 2: Stock & Tracking --}}
        {{-- ============================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock &amp; Tracking</h2>

            @if($tracking_method === 'quantity')
                {{-- Quantity-based fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="quantity_in_stock" class="block text-sm font-medium text-gray-700 mb-1">Quantity in Stock <span class="text-red-500">*</span></label>
                        <input type="number" id="quantity_in_stock" wire:model="quantity_in_stock" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('quantity_in_stock') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                        <input type="number" id="low_stock_threshold" wire:model="low_stock_threshold" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('low_stock_threshold') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Size / Specification</label>
                    <div class="flex items-start gap-4">
                        <div class="inline-flex rounded-lg bg-gray-100 p-0.5 text-xs font-medium shrink-0">
                            <button type="button" wire:click="$set('sizeMode', 'simple')"
                                class="rounded-md px-3 py-1.5 transition-all {{ $sizeMode === 'simple' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Single Value
                            </button>
                            <button type="button" wire:click="$set('sizeMode', 'dimensions')"
                                class="rounded-md px-3 py-1.5 transition-all {{ $sizeMode === 'dimensions' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                W &times; L &times; H
                            </button>
                        </div>
                        <div class="flex-1">
                            @if($sizeMode === 'simple')
                                <input type="text" wire:model="size" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. A4, 500ml, 15-inch">
                            @else
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500">W</span>
                                        <input type="number" step="any" min="0" wire:model="dim_width" class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                                    </div>
                                    <span class="text-gray-400 font-medium">&times;</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500">L</span>
                                        <input type="number" step="any" min="0" wire:model="dim_length" class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                                    </div>
                                    <span class="text-gray-400 font-medium">&times;</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500">H</span>
                                        <input type="number" step="any" min="0" wire:model="dim_height" class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                                    </div>
                                    <select wire:model="dim_unit" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="cm">cm</option>
                                        <option value="mm">mm</option>
                                        <option value="m">m</option>
                                        <option value="in">in</option>
                                        <option value="ft">ft</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Leave any dimension blank if not applicable.</p>
                            @endif
                            @error('size') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @else
                {{-- Individual tracking: Asset Units table --}}
                <p class="text-sm text-gray-500 mb-3">Add each individual unit with its serial number, asset tag, and condition.</p>

                @error('assetUnits') <p class="mb-2 text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Asset Tag</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serial Number</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Condition <span class="text-red-500">*</span></th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($assetUnits as $idx => $unit)
                                <tr wire:key="unit-{{ $idx }}">
                                    <td class="px-3 py-2 text-gray-400">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-2">
                                        <input type="text" wire:model="assetUnits.{{ $idx }}.asset_tag" class="w-full rounded border-gray-300 text-sm" placeholder="IIT/2024/001">
                                        @error("assetUnits.{$idx}.asset_tag") <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" wire:model="assetUnits.{{ $idx }}.serial_number" class="w-full rounded border-gray-300 text-sm" placeholder="SN-001">
                                        @error("assetUnits.{$idx}.serial_number") <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-3 py-2">
                                        <select wire:model="assetUnits.{{ $idx }}.condition_status" class="w-full rounded border-gray-300 text-sm">
                                            @foreach(\App\Enums\ConditionStatus::cases() as $cond)
                                                <option value="{{ $cond->value }}">{{ ucfirst($cond->value) }}</option>
                                            @endforeach
                                        </select>
                                        @error("assetUnits.{$idx}.condition_status") <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" wire:model="assetUnits.{{ $idx }}.assigned_staff_name_snapshot" class="w-full rounded border-gray-300 text-sm" placeholder="Staff name">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" wire:model="assetUnits.{{ $idx }}.current_location" class="w-full rounded border-gray-300 text-sm" placeholder="Room 101">
                                    </td>
                                    <td class="px-3 py-2">
                                        @if(count($assetUnits) > 1)
                                            <button type="button" wire:click="removeAssetUnit({{ $idx }})" class="text-red-500 hover:text-red-700" title="Remove unit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="button" wire:click="addAssetUnit" class="mt-3 inline-flex items-center gap-1 rounded-lg border border-dashed border-gray-400 px-3 py-1.5 text-sm text-gray-600 hover:border-blue-500 hover:text-blue-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Unit
                </button>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                        <input type="number" id="low_stock_threshold" wire:model="low_stock_threshold" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('low_stock_threshold') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Size / Specification</label>
                    <div class="flex items-start gap-4">
                        <div class="inline-flex rounded-lg bg-gray-100 p-0.5 text-xs font-medium shrink-0">
                            <button type="button" wire:click="$set('sizeMode', 'simple')"
                                class="rounded-md px-3 py-1.5 transition-all {{ $sizeMode === 'simple' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Single Value
                            </button>
                            <button type="button" wire:click="$set('sizeMode', 'dimensions')"
                                class="rounded-md px-3 py-1.5 transition-all {{ $sizeMode === 'dimensions' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                W &times; L &times; H
                            </button>
                        </div>
                        <div class="flex-1">
                            @if($sizeMode === 'simple')
                                <input type="text" wire:model="size" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. 15-inch, 256GB SSD">
                            @else
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500">W</span>
                                        <input type="number" step="any" min="0" wire:model="dim_width" class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                                    </div>
                                    <span class="text-gray-400 font-medium">&times;</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500">L</span>
                                        <input type="number" step="any" min="0" wire:model="dim_length" class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                                    </div>
                                    <span class="text-gray-400 font-medium">&times;</span>
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-500">H</span>
                                        <input type="number" step="any" min="0" wire:model="dim_height" class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="0">
                                    </div>
                                    <select wire:model="dim_unit" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="cm">cm</option>
                                        <option value="mm">mm</option>
                                        <option value="m">m</option>
                                        <option value="in">in</option>
                                        <option value="ft">ft</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Leave any dimension blank if not applicable.</p>
                            @endif
                            @error('size') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================================== --}}
        {{-- SECTION 4: Location --}}
        {{-- ============================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Location</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="floor" class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                    <input type="text" id="floor" wire:model="floor" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Ground, 1st, 2nd">
                    @error('floor') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="venue" class="block text-sm font-medium text-gray-700 mb-1">Venue / Room</label>
                    <input type="text" id="venue" wire:model="venue" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Room 101, Lab A">
                    @error('venue') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="venue_storage" class="block text-sm font-medium text-gray-700 mb-1">Storage Area</label>
                    <input type="text" id="venue_storage" wire:model="venue_storage" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Cabinet A, Shelf 3">
                    @error('venue_storage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- SECTION 5: Custom Fields --}}
        {{-- ============================================================== --}}
        @if($customFields->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Custom Fields</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($customFields as $cf)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $cf->label }}
                            @if($cf->is_required) <span class="text-red-500">*</span> @endif
                        </label>

                        @switch($cf->field_type->value)
                            @case('textarea')
                                <textarea wire:model="customFieldValues.{{ $cf->id }}" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                                @break
                            @case('select')
                                <select wire:model="customFieldValues.{{ $cf->id }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">— Select —</option>
                                    @foreach(json_decode($cf->options_json ?? '[]', true) as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                                @break
                            @case('boolean')
                                <select wire:model="customFieldValues.{{ $cf->id }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">— Select —</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                @break
                            @case('date')
                                <input type="date" wire:model="customFieldValues.{{ $cf->id }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @break
                            @case('number')
                                <input type="number" wire:model="customFieldValues.{{ $cf->id }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                @break
                            @default
                                <input type="text" wire:model="customFieldValues.{{ $cf->id }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @endswitch

                        @error("customFieldValues.{$cf->id}") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- SECTION 6: Extra Fields (Ad-hoc) --}}
        {{-- ============================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Extra Fields</h2>
            <p class="text-sm text-gray-500 mb-4">Add any additional data fields specific to this item. Check "Reuse" to make the field available for future items.</p>

            @foreach($extraFields as $idx => $ef)
                <div wire:key="ef-{{ $idx }}" class="flex flex-wrap items-end gap-3 mb-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Label</label>
                        <input type="text" wire:model="extraFields.{{ $idx }}.label" class="w-full rounded border-gray-300 text-sm" placeholder="Field name">
                    </div>
                    <div class="w-28">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                        <select wire:model.live="extraFields.{{ $idx }}.type" class="w-full rounded border-gray-300 text-sm">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="boolean">Yes/No</option>
                        </select>
                    </div>
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                        @if(($extraFields[$idx]['type'] ?? 'text') === 'boolean')
                            <select wire:model="extraFields.{{ $idx }}.value" class="w-full rounded border-gray-300 text-sm">
                                <option value="">— Select —</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        @elseif(($extraFields[$idx]['type'] ?? 'text') === 'date')
                            <input type="date" wire:model="extraFields.{{ $idx }}.value" class="w-full rounded border-gray-300 text-sm">
                        @elseif(($extraFields[$idx]['type'] ?? 'text') === 'number')
                            <input type="number" wire:model="extraFields.{{ $idx }}.value" class="w-full rounded border-gray-300 text-sm">
                        @else
                            <input type="text" wire:model="extraFields.{{ $idx }}.value" class="w-full rounded border-gray-300 text-sm" placeholder="Value">
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="inline-flex items-center gap-1 text-xs text-gray-600 cursor-pointer">
                            <input type="checkbox" wire:model="extraFields.{{ $idx }}.save_for_future" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            Reuse
                        </label>
                        <button type="button" wire:click="removeExtraField({{ $idx }})" class="text-red-500 hover:text-red-700" title="Remove field">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach

            <button type="button" wire:click="addExtraField" class="inline-flex items-center gap-1 rounded-lg border border-dashed border-gray-400 px-3 py-1.5 text-sm text-gray-600 hover:border-blue-500 hover:text-blue-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Extra Field
            </button>
        </div>

        {{-- ============================================================== --}}
        {{-- SECTION 7: Remarks & Status --}}
        {{-- ============================================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Remarks &amp; Status</h2>

            <div class="space-y-4">
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Description / Notes</label>
                    <textarea id="remarks" wire:model="remarks" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Brief description, additional notes about this item..."></textarea>
                    @error('remarks') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                    <span class="text-sm font-medium text-gray-700">Active Item</span>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- SUBMIT --}}
        {{-- ============================================================== --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition disabled:opacity-50" wire:loading.attr="disabled">
                <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Create Item
            </button>
        </div>

    </form>
</div>
