<div>
    <div class="mb-6"><a href="{{ route('users.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg> Back</a><h2 class="mt-2 text-2xl font-bold text-gray-900">Add User</h2></div>
    <form wire:submit="save"><div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100"><div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div><label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label><input wire:model="name" type="text" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">@error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label><input wire:model="email" type="email" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">@error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label><input wire:model="password" type="password" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">@error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label class="block text-sm font-medium text-gray-700">Department <span class="text-red-500">*</span></label><select wire:model="department_id" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"><option value="">Select department</option>@foreach($departments as $dept)<option value="{{ $dept->id }}">{{ $dept->name }}</option>@endforeach</select>@error('department_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div class="sm:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label><p class="text-xs text-gray-500 mb-3">Select a role for this user.</p><div class="grid grid-cols-2 gap-2 sm:grid-cols-3">@foreach($roles as $role)<label class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer transition-colors {{ (string) $role->id === $selectedRole ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 hover:bg-gray-50' }}"><input wire:model.live="selectedRole" type="radio" name="selectedRole" value="{{ $role->id }}" class="border-gray-300 text-blue-600 focus:ring-blue-500"><span class="text-gray-700">{{ $role->name }}</span></label>@endforeach</div>@error('selectedRole')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div class="flex items-center gap-3"><label class="relative inline-flex cursor-pointer items-center"><input wire:model="is_active" type="checkbox" class="peer sr-only"><div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"></div></label><span class="text-sm font-medium text-gray-700">Active</span></div>
    </div></div>

    {{-- Additional Inventory Access --}}
    <div class="mt-6 rounded-xl bg-white p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Additional Inventory Access</h3>
                <p class="text-sm text-gray-500 mt-0.5">Grant this user access to inventory beyond their own department.</p>
            </div>
            <label class="relative inline-flex cursor-pointer items-center">
                <input wire:model.live="showAdditionalAccess" type="checkbox" class="peer sr-only">
                <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
            </label>
        </div>

        @if($showAdditionalAccess)
        <div class="mt-5 space-y-5">
            {{-- View All Inventory Toggle --}}
            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                <label class="relative inline-flex cursor-pointer items-center">
                    <input wire:model.live="can_view_all_inventory" type="checkbox" class="peer sr-only">
                    <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                </label>
                <div>
                    <span class="text-sm font-medium text-gray-700">View All Inventory</span>
                    <p class="text-xs text-gray-500">Allow user to see inventory across all departments and categories</p>
                </div>
            </div>

            @if(!$can_view_all_inventory)
            {{-- Additional Departments --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Department Access</label>
                <p class="text-xs text-gray-500 mb-3">User already has access to their primary department. Select extra departments below.</p>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach($departments as $dept)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50 transition-colors {{ $department_id == $dept->id ? 'opacity-50' : '' }}">
                            <input wire:model="accessibleDepartments" type="checkbox" value="{{ $dept->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ $department_id == $dept->id ? 'disabled' : '' }}>
                            <span class="text-gray-700">{{ $dept->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Additional Categories --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Access</label>
                <p class="text-xs text-gray-500 mb-3">Restrict to specific categories (leave all unchecked to allow all categories within accessible departments).</p>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach($allCategories as $cat)
                        <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50 transition-colors">
                            <input wire:model="accessibleCategories" type="checkbox" value="{{ $cat->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
    <div class="mt-6 flex justify-end gap-3"><a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a><button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Create User</button></div></form>
</div>
