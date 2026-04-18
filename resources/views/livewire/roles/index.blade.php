<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Roles & Permissions</h2>
            <p class="mt-1 text-sm text-gray-500">Manage roles and their associated permissions</p>
        </div>
        <a href="{{ route('roles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create Role
        </a>
    </div>

    @php
        $permissionGroups = [
            'Administration' => ['manage_users', 'manage_roles_permissions', 'view_audit_log'],
            'Inventory' => ['manage_inventory', 'archive_inventory', 'manage_categories', 'manage_departments', 'manage_custom_fields'],
            'Operations' => ['issue_items', 'receive_returns', 'adjust_stock', 'manage_repairs'],
            'Reporting' => ['view_dashboard', 'export_data', 'import_csv'],
        ];
    @endphp

    <div class="space-y-6">
        @foreach($roles as $role)
        <div class="rounded-xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            {{-- Role Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $role->code === 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $role->name }}</h3>
                        <div class="flex items-center gap-3 mt-0.5">
                            @if($role->description)<span class="text-sm text-gray-500">{{ $role->description }}</span><span class="text-gray-300">·</span>@endif
                            <span class="text-sm text-gray-500">{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-sm text-gray-500">{{ $role->permissions->count() }} {{ Str::plural('permission', $role->permissions->count()) }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('roles.edit', $role) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                    Edit
                </a>
            </div>

            {{-- Permissions Grid --}}
            <div class="px-6 py-4">
                @if($role->code === 'admin')
                    <div class="flex items-center gap-2 rounded-lg bg-purple-50 px-4 py-3">
                        <svg class="h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span class="text-sm font-medium text-purple-700">Full access — all permissions granted</span>
                    </div>
                @elseif($role->permissions->isEmpty())
                    <p class="text-sm text-gray-400 py-2">No permissions assigned</p>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($permissionGroups as $group => $codes)
                            @php $groupPerms = $role->permissions->whereIn('code', $codes); @endphp
                            @if($groupPerms->isNotEmpty())
                            <div>
                                <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">{{ $group }}</h4>
                                <div class="space-y-1">
                                    @foreach($codes as $code)
                                        @php $hasPerm = $role->permissions->contains('code', $code); @endphp
                                        <div class="flex items-center gap-2 text-sm {{ $hasPerm ? 'text-gray-700' : 'text-gray-300' }}">
                                            @if($hasPerm)
                                                <svg class="h-3.5 w-3.5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                            @else
                                                <svg class="h-3.5 w-3.5 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                            @endif
                                            @php $permModel = $role->permissions->firstWhere('code', $code) ?? \App\Models\Permission::where('code', $code)->first(); @endphp
                                            <span>{{ $permModel?->name ?? Str::headline(str_replace('_', ' ', $code)) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
