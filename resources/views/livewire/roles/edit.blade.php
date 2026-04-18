<div x-data="{
    selected: @entangle('selectedPermissions').live,
    toggle(id) {
        id = String(id);
        const idx = this.selected.indexOf(id);
        if (idx === -1) { this.selected.push(id); } else { this.selected.splice(idx, 1); }
    },
    has(id) { return this.selected.includes(String(id)); }
}">
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                Back to Roles
            </a>
            <h2 class="mt-2 text-xl font-bold text-gray-900">Edit Role: {{ $role->name }}</h2>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 self-start sm:self-auto">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            <span x-text="selected.length + ' of {{ $permissions->count() }} permissions'"></span>
        </span>
    </div>

    <form wire:submit="save">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            {{-- Role Details --}}
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="edit-role-name" class="block text-sm font-medium text-gray-700 mb-1.5">Role Name <span class="text-red-500">*</span></label>
                        <input wire:model="name" id="edit-role-name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="edit-role-desc" class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <input wire:model="description" id="edit-role-desc" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="p-4 sm:p-6 space-y-8">
                {{-- Administration --}}
                <div class="border-l-4 border-l-purple-400 pl-4 sm:pl-5">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-4">Administration</h4>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach(['manage_users', 'manage_roles_permissions', 'view_audit_log'] as $code)
                            @php $perm = $permissions->firstWhere('code', $code); @endphp
                            @if($perm)
                            <button type="button" @click="toggle({{ $perm->id }})"
                                :class="has({{ $perm->id }}) ? 'bg-purple-600 text-white border-purple-600 shadow-sm' : 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100'"
                                class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition-all duration-100 cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:ring-offset-1">
                                {{ $perm->name }}
                            </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Inventory --}}
                <div class="border-l-4 border-l-blue-400 pl-4 sm:pl-5">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-4">Inventory</h4>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach(['manage_inventory', 'archive_inventory', 'manage_categories', 'manage_departments', 'manage_custom_fields'] as $code)
                            @php $perm = $permissions->firstWhere('code', $code); @endphp
                            @if($perm)
                            <button type="button" @click="toggle({{ $perm->id }})"
                                :class="has({{ $perm->id }}) ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100'"
                                class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition-all duration-100 cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:ring-offset-1">
                                {{ $perm->name }}
                            </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Operations --}}
                <div class="border-l-4 border-l-green-400 pl-4 sm:pl-5">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-4">Operations</h4>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach(['issue_items', 'receive_returns', 'adjust_stock', 'manage_repairs'] as $code)
                            @php $perm = $permissions->firstWhere('code', $code); @endphp
                            @if($perm)
                            <button type="button" @click="toggle({{ $perm->id }})"
                                :class="has({{ $perm->id }}) ? 'bg-green-600 text-white border-green-600 shadow-sm' : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'"
                                class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition-all duration-100 cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-green-500/40 focus:ring-offset-1">
                                {{ $perm->name }}
                            </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Reporting --}}
                <div class="border-l-4 border-l-amber-400 pl-4 sm:pl-5">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-4">Reporting</h4>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach(['view_dashboard', 'export_data', 'import_csv'] as $code)
                            @php $perm = $permissions->firstWhere('code', $code); @endphp
                            @if($perm)
                            <button type="button" @click="toggle({{ $perm->id }})"
                                :class="has({{ $perm->id }}) ? 'bg-amber-600 text-white border-amber-600 shadow-sm' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'"
                                class="rounded-full border px-3.5 py-1.5 text-xs font-medium transition-all duration-100 cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:ring-offset-1">
                                {{ $perm->name }}
                            </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
                    <span wire:loading.remove wire:target="save">Save Changes</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
