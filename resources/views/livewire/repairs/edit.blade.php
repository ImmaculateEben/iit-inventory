<div>
    <div class="mb-6">
        <a href="{{ route('repairs.show', $repairRecord) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Record
        </a>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Edit {{ ucfirst($action_type) }} Record</h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                {{-- Action Type Toggle --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action Type <span class="text-red-500">*</span></label>
                    <div class="inline-flex rounded-lg bg-gray-100 p-1">
                        <button type="button" wire:click="$set('action_type', 'repair')"
                            class="rounded-md px-4 py-2 text-sm font-medium transition-all {{ $action_type === 'repair' ? 'bg-white shadow text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">
                            <svg class="inline-block h-4 w-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085"/></svg>
                            Repair
                        </button>
                        <button type="button" wire:click="$set('action_type', 'replacement')"
                            class="rounded-md px-4 py-2 text-sm font-medium transition-all {{ $action_type === 'replacement' ? 'bg-white shadow text-orange-700' : 'text-gray-500 hover:text-gray-700' }}">
                            <svg class="inline-block h-4 w-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3"/></svg>
                            Replacement
                        </button>
                    </div>
                </div>

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

                {{-- What Was Repaired/Replaced --}}
                <div>
                    <label for="component_repaired" class="block text-sm font-medium text-gray-700">What Was {{ $action_type === 'replacement' ? 'Replaced' : 'Repaired' }} <span class="text-red-500">*</span></label>
                    <input wire:model="component_repaired" type="text" id="component_repaired"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="e.g. Hard disk, Screen, Motherboard, Toner">
                    @error('component_repaired') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label for="repair_date" class="block text-sm font-medium text-gray-700">{{ $action_type === 'replacement' ? 'Replacement' : 'Repair' }} Date <span class="text-red-500">*</span></label>
                    <input wire:model="repair_date" type="date" id="repair_date"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('repair_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select wire:model="status" id="status" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @foreach(\App\Enums\RepairStatus::cases() as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="sm:col-span-2">
                    <label for="repair_description" class="block text-sm font-medium text-gray-700">{{ $action_type === 'replacement' ? 'Replacement' : 'Repair' }} Description <span class="text-red-500">*</span></label>
                    <textarea wire:model="repair_description" id="repair_description" rows="3"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Describe the {{ $action_type }} work done..."></textarea>
                    @error('repair_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('repairs.show', $repairRecord) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
                <span wire:loading.remove wire:target="save">Update Record</span>
                <span wire:loading wire:target="save">Updating...</span>
            </button>
        </div>
    </form>
</div>
