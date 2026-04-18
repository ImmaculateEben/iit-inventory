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
                <div class="sm:col-span-2">
                    <label for="inventory_item_id" class="block text-sm font-medium text-gray-700">Item <span class="text-red-500">*</span></label>
                    <select wire:model="inventory_item_id" id="inventory_item_id" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->item_code }} — {{ $item->item_name }}</option>
                        @endforeach
                    </select>
                    @error('inventory_item_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="fault_description" class="block text-sm font-medium text-gray-700">Fault Description <span class="text-red-500">*</span></label>
                    <textarea wire:model="fault_description" id="fault_description" rows="3" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    @error('fault_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="vendor_name" class="block text-sm font-medium text-gray-700">Vendor / Repair Shop</label>
                    <input wire:model="vendor_name" type="text" id="vendor_name" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="sent_date" class="block text-sm font-medium text-gray-700">Sent Date</label>
                    <input wire:model="sent_date" type="date" id="sent_date" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
