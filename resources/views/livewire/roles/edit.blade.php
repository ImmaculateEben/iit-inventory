<div>
    <div class="mb-6"><a href="{{ route('roles.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg> Back</a><h2 class="mt-2 text-2xl font-bold text-gray-900">Edit Role: {{ $role->name }}</h2></div>
    <form wire:submit="save"><div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100 space-y-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div><label class="block text-sm font-medium text-gray-700">Role Name <span class="text-red-500">*</span></label><input wire:model="name" type="text" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium text-gray-700">Description</label><input wire:model="description" type="text" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($permissions as $perm)
                <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 hover:bg-gray-50 cursor-pointer">
                    <input wire:model="selectedPermissions" type="checkbox" value="{{ $perm->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">{{ $perm->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
    </div>
    <div class="mt-6 flex justify-end gap-3"><a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a><button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Save Changes</button></div></form>
</div>
