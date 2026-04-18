<div>
    <div class="mb-6">
        <a href="{{ route('issues.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Issues
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Issue Item</h1>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                {{-- Searchable Item Select --}}
                <div class="relative" x-data="{ open: @entangle('showItemDropdown') }" @click.outside="open = false">
                    <label class="block text-sm font-medium text-gray-700">Item <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="itemSearch"
                            @focus="if (!$wire.inventory_item_id) open = true"
                            placeholder="Search by name or code..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pr-8"
                            autocomplete="off"
                        >
                        @if($inventory_item_id)
                            <button type="button" wire:click="clearItem" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @else
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Dropdown results --}}
                    @if($showItemDropdown && $filteredItems->count())
                        <ul class="absolute z-20 mt-1 w-full max-h-60 overflow-auto rounded-lg bg-white border border-gray-200 shadow-lg text-sm">
                            @foreach($filteredItems as $item)
                                <li wire:click="selectItem({{ $item->id }})"
                                    class="cursor-pointer px-4 py-2.5 hover:bg-blue-50 transition">
                                    <span class="font-medium text-gray-900">{{ $item->item_code }}</span>
                                    <span class="text-gray-500">— {{ $item->item_name }}</span>
                                    <span class="ml-1 text-xs text-emerald-600">({{ $item->quantity_available }} avail.)</span>
                                </li>
                            @endforeach
                        </ul>
                    @elseif($showItemDropdown && strlen($itemSearch) > 0 && $filteredItems->isEmpty())
                        <div class="absolute z-20 mt-1 w-full rounded-lg bg-white border border-gray-200 shadow-lg px-4 py-3 text-sm text-gray-500">
                            No items found matching "{{ $itemSearch }}"
                        </div>
                    @endif

                    @error('inventory_item_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    @if($maxQuantity > 0)
                        <p class="mt-1 text-xs text-gray-500">Available stock: <strong>{{ $maxQuantity }}</strong></p>
                    @endif
                </div>

                {{-- Staff Name with Autocomplete --}}
                <div class="relative" x-data="{ open: @entangle('showStaffDropdown') }" @click.outside="open = false">
                    <label class="block text-sm font-medium text-gray-700">Issue To <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="staffSearch"
                        @focus="if (staffSearch.length >= 2) open = true"
                        placeholder="Type staff/person name..."
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        autocomplete="off"
                    >

                    {{-- Past names dropdown --}}
                    @if($showStaffDropdown && $pastStaffNames->count())
                        <ul class="absolute z-20 mt-1 w-full max-h-48 overflow-auto rounded-lg bg-white border border-gray-200 shadow-lg text-sm">
                            @foreach($pastStaffNames as $name)
                                <li wire:click="selectStaff('{{ addslashes($name) }}')"
                                    class="cursor-pointer px-4 py-2.5 hover:bg-blue-50 transition text-gray-700">
                                    {{ $name }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @error('staff_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-400">Previously used names will appear as suggestions.</p>
                </div>

                {{-- Quantity --}}
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        id="quantity"
                        wire:model.live="quantity"
                        min="1"
                        max="{{ $maxQuantity ?: 99999 }}"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    >
                    @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @if($maxQuantity > 0 && $maxQuantity <= 3)
                        <p class="mt-1 text-xs text-amber-600">Only {{ $maxQuantity }} unit{{ $maxQuantity > 1 ? 's' : '' }} available.</p>
                    @endif
                </div>

                {{-- Notes --}}
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">Notes</label>
                    <input
                        type="text"
                        id="note"
                        wire:model="note"
                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        placeholder="Optional notes"
                    >
                </div>

            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('issues.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition disabled:opacity-50" wire:loading.attr="disabled">
                <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span wire:loading.remove wire:target="save">Issue Item</span>
                <span wire:loading wire:target="save">Processing...</span>
            </button>
        </div>
    </form>
</div>
