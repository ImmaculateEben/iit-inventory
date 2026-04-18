<div>
    <div class="mb-6">
        <a href="{{ route('returns.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Returns
        </a>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Record Return</h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="issue_record_id" class="block text-sm font-medium text-gray-700">Issue Record <span class="text-red-500">*</span></label>
                    <select wire:model.live="issue_record_id" id="issue_record_id" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select issue record</option>
                        @foreach($issueRecords as $issue)
                            <option value="{{ $issue->id }}">{{ $issue->issue_date->format('M d') }} — {{ $issue->inventoryItem?->item_name }} (Qty: {{ $issue->quantity_issued }})</option>
                        @endforeach
                    </select>
                    @error('issue_record_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="quantity_returned" class="block text-sm font-medium text-gray-700">Quantity Returned <span class="text-red-500">*</span></label>
                    <input wire:model="quantity_returned" type="number" id="quantity_returned" min="1" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('quantity_returned') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="condition_on_return" class="block text-sm font-medium text-gray-700">Condition <span class="text-red-500">*</span></label>
                    <select wire:model="condition_on_return" id="condition_on_return" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="good">Good</option>
                        <option value="damaged">Damaged</option>
                        <option value="faulty">Faulty</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea wire:model="notes" id="notes" rows="2" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('returns.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
                <span wire:loading.remove wire:target="save">Record Return</span>
                <span wire:loading wire:target="save">Processing...</span>
            </button>
        </div>
    </form>
</div>
