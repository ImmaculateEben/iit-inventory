<div>
    <div class="mb-6">
        <a href="{{ route('repairs.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Repairs
        </a>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Report Repair</h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                {{-- Searchable Item Selection --}}
                <div class="sm:col-span-2" x-data="{ open: @entangle('showItemDropdown') }">
                    <label class="block text-sm font-medium text-gray-700">Item <span class="text-red-500">*</span></label>

                    @if($selectedItemLabel)
                        <div class="mt-1 flex items-center gap-2 rounded-lg border border-gray-300 bg-gray-50 px-3 py-2">
                            <svg class="h-4 w-4 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            <span class="text-sm text-gray-900 flex-1">{{ $selectedItemLabel }}</span>
                            <button type="button" wire:click="clearItem" class="text-gray-400 hover:text-red-500">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @else
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="itemSearch" placeholder="Search by name, code, model, manufacturer..."
                                class="w-full rounded-lg border-gray-300 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                @focus="open = true" @click.away="open = false">

                            @if($showItemDropdown && count($filteredItems))
                            <ul class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg" @click.away="open = false">
                                @foreach($filteredItems as $item)
                                <li wire:click="selectItem({{ $item->id }})" class="cursor-pointer px-4 py-2 text-sm hover:bg-blue-50 transition">
                                    <div class="font-medium text-gray-900">{{ $item->item_name }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if($item->item_code){{ $item->item_code }}@endif
                                        @if($item->manufacturer) &middot; {{ $item->manufacturer }}@endif
                                        @if($item->model_number) &middot; {{ $item->model_number }}@endif
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @elseif($showItemDropdown && strlen($itemSearch) >= 1)
                            <div class="absolute z-20 mt-1 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-500 shadow-lg">
                                No items found matching "{{ $itemSearch }}"
                            </div>
                            @endif
                        </div>
                    @endif
                    @error('inventory_item_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- What Was Repaired --}}
                <div>
                    <label for="component_repaired" class="block text-sm font-medium text-gray-700">What Was Repaired <span class="text-red-500">*</span></label>
                    <input wire:model="component_repaired" type="text" id="component_repaired"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="e.g. Hard disk, Screen, Motherboard, Toner">
                    @error('component_repaired') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Repair Date --}}
                <div>
                    <label for="repair_date" class="block text-sm font-medium text-gray-700">Repair Date <span class="text-red-500">*</span></label>
                    <input wire:model="repair_date" type="date" id="repair_date"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('repair_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Repair Description --}}
                <div class="sm:col-span-2">
                    <label for="repair_description" class="block text-sm font-medium text-gray-700">Repair Description <span class="text-red-500">*</span></label>
                    <textarea wire:model="repair_description" id="repair_description" rows="3"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Describe the repair work done..."></textarea>
                    @error('repair_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('repairs.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
                <span wire:loading.remove wire:target="save">Submit Report</span>
                <span wire:loading wire:target="save">Submitting...</span>
            </button>
        </div>
    </form>
</div>
