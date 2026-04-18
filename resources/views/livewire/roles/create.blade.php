<div>
    <div class="mb-6">
        <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Roles
        </a>
        <h2 class="mt-2 text-2xl font-bold text-gray-900">Create Role</h2>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Role Details --}}
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Role Details</h3>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="role-name" class="block text-sm font-medium text-gray-700 mb-1">Role Name <span class="text-red-500">*</span></label>
                    <input wire:model="name" id="role-name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Lab Technician">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="role-desc" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input wire:model="description" id="role-desc" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Brief description of this role">
                </div>
            </div>
        </div>

        {{-- Permissions --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Permissions</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Select which permissions this role should have</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ count($selectedPermissions) }} / {{ $permissions->count() }}</span>
            </div>

            @php
                $groups = [
                    'Administration' => ['icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z', 'icon2' => 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z', 'color' => 'purple', 'desc' => 'User accounts, roles, and audit trail', 'codes' => ['manage_users', 'manage_roles_permissions', 'view_audit_log']],
                    'Inventory' => ['icon' => 'm20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z', 'icon2' => null, 'color' => 'blue', 'desc' => 'Items, categories, departments, and fields', 'codes' => ['manage_inventory', 'archive_inventory', 'manage_categories', 'manage_departments', 'manage_custom_fields']],
                    'Operations' => ['icon' => 'M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5', 'icon2' => null, 'color' => 'green', 'desc' => 'Issues, returns, repairs, and adjustments', 'codes' => ['issue_items', 'receive_returns', 'adjust_stock', 'manage_repairs']],
                    'Reporting' => ['icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z', 'icon2' => null, 'color' => 'amber', 'desc' => 'Dashboard, data export, and imports', 'codes' => ['view_dashboard', 'export_data', 'import_csv']],
                ];
                $colorClasses = [
                    'purple' => 'bg-purple-50 text-purple-600 border-purple-200',
                    'blue' => 'bg-blue-50 text-blue-600 border-blue-200',
                    'green' => 'bg-green-50 text-green-600 border-green-200',
                    'amber' => 'bg-amber-50 text-amber-600 border-amber-200',
                ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 p-6">
                @foreach($groups as $groupName => $group)
                <div class="rounded-lg border border-gray-200 overflow-hidden">
                    {{-- Group Header --}}
                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <div class="flex h-8 w-8 items-center justify-center rounded-md {{ $colorClasses[$group['color']] }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $group['icon'] }}" />
                                @if($group['icon2'])<path stroke-linecap="round" stroke-linejoin="round" d="{{ $group['icon2'] }}" />@endif
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900">{{ $groupName }}</h4>
                            <p class="text-xs text-gray-500 leading-tight">{{ $group['desc'] }}</p>
                        </div>
                    </div>

                    {{-- Permission Items --}}
                    <div class="divide-y divide-gray-100">
                        @foreach($group['codes'] as $code)
                            @php $perm = $permissions->firstWhere('code', $code); @endphp
                            @if($perm)
                            <label class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50 cursor-pointer transition">
                                <span class="text-sm text-gray-700">{{ $perm->name }}</span>
                                <input wire:model="selectedPermissions" type="checkbox" value="{{ $perm->id }}" role="switch"
                                    class="relative h-7 w-12 shrink-0 cursor-pointer appearance-none rounded-full bg-gray-300 transition-colors duration-200 checked:bg-emerald-500 before:absolute before:left-1 before:top-1 before:h-5 before:w-5 before:rounded-full before:bg-white before:shadow before:ring-1 before:ring-gray-200 before:transition-all before:duration-200 before:content-[''] checked:before:left-6 checked:before:ring-0 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            </label>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
                <span wire:loading.remove wire:target="save">Create Role</span>
                <span wire:loading wire:target="save">Creating...</span>
            </button>
        </div>
    </form>
</div>
