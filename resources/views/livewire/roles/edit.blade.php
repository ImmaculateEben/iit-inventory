<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Roles
            </a>
            <h2 class="mt-1 text-xl font-bold text-gray-900">Edit Role: {{ $role->name }}</h2>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            {{ count($selectedPermissions) }} of {{ $permissions->count() }} permissions
        </span>
    </div>

    <form wire:submit="save">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            {{-- Role Details --}}
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="edit-role-name" class="block text-sm font-medium text-gray-700 mb-1">Role Name <span class="text-red-500">*</span></label>
                        <input wire:model="name" id="edit-role-name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="edit-role-desc" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input wire:model="description" id="edit-role-desc" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            @php
                $groups = [
                    'Administration' => ['color' => 'purple', 'border' => 'border-l-purple-400', 'bg' => 'bg-purple-500', 'light' => 'bg-purple-50 text-purple-700 border-purple-200', 'selected' => 'bg-purple-600 text-white border-purple-600', 'codes' => ['manage_users', 'manage_roles_permissions', 'view_audit_log']],
                    'Inventory' => ['color' => 'blue', 'border' => 'border-l-blue-400', 'bg' => 'bg-blue-500', 'light' => 'bg-blue-50 text-blue-700 border-blue-200', 'selected' => 'bg-blue-600 text-white border-blue-600', 'codes' => ['manage_inventory', 'archive_inventory', 'manage_categories', 'manage_departments', 'manage_custom_fields']],
                    'Operations' => ['color' => 'green', 'border' => 'border-l-green-400', 'bg' => 'bg-green-500', 'light' => 'bg-green-50 text-green-700 border-green-200', 'selected' => 'bg-green-600 text-white border-green-600', 'codes' => ['issue_items', 'receive_returns', 'adjust_stock', 'manage_repairs']],
                    'Reporting' => ['color' => 'amber', 'border' => 'border-l-amber-400', 'bg' => 'bg-amber-500', 'light' => 'bg-amber-50 text-amber-700 border-amber-200', 'selected' => 'bg-amber-600 text-white border-amber-600', 'codes' => ['view_dashboard', 'export_data', 'import_csv']],
                ];
            @endphp

            <div class="px-6 py-5 space-y-5">
                @foreach($groups as $groupName => $group)
                <div class="border-l-4 {{ $group['border'] }} pl-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2.5">{{ $groupName }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($group['codes'] as $code)
                            @php
                                $perm = $permissions->firstWhere('code', $code);
                                $isSelected = $perm && in_array($perm->id, $selectedPermissions);
                            @endphp
                            @if($perm)
                            <label class="cursor-pointer">
                                <input wire:model="selectedPermissions" type="checkbox" value="{{ $perm->id }}" class="hidden">
                                <span class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-medium transition-all duration-150 hover:shadow-sm {{ $isSelected ? $group['selected'] : $group['light'] }}">
                                    @if($isSelected)
                                    <svg class="-ml-0.5 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    @endif
                                    {{ $perm->name }}
                                </span>
                            </label>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
                    <span wire:loading.remove wire:target="save">Save Changes</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
