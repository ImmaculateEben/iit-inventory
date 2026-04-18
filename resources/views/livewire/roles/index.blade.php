<div>
    <div class="mb-6"><h2 class="text-2xl font-bold text-gray-900">Roles & Permissions</h2><p class="mt-1 text-sm text-gray-500">Manage roles and their permissions</p></div>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        @foreach($roles as $role)
        <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $role->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $role->users_count }} user(s)</p>
                </div>
                <a href="{{ route('roles.edit', $role) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Edit</a>
            </div>
            @if($role->description)<p class="text-sm text-gray-600 mb-3">{{ $role->description }}</p>@endif
            <div class="flex flex-wrap gap-1.5">
                @foreach($role->permissions as $perm)
                <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ $perm->name }}</span>
                @endforeach
                @if($role->permissions->isEmpty())<span class="text-xs text-gray-400">No permissions assigned</span>@endif
            </div>
        </div>
        @endforeach
    </div>
</div>
