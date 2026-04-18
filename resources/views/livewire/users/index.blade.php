<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h2 class="text-2xl font-bold text-gray-900">Users</h2><p class="mt-1 text-sm text-gray-500">Manage system users and access</p></div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 transition"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>Add User</a>
    </div>
    <div class="mb-6 rounded-xl bg-white p-4 shadow-sm border border-gray-100">
        <div class="relative sm:max-w-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..." class="w-full pl-10">
        </div>
    </div>
    <div class="overflow-hidden rounded-xl bg-white shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50"><tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3"></th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $u)
                    <tr class="hover:bg-gray-50/50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $u->name }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $u->email }}</td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $u->department?->name }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($u->roles as $role)
                                <span class="inline-flex rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4"><span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $u->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}"><span class="h-1.5 w-1.5 rounded-full {{ $u->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm"><a href="{{ route('users.edit', $u) }}" class="text-blue-600 hover:text-blue-800">Edit</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())<div class="border-t border-gray-100 px-6 py-4">{{ $users->links() }}</div>@endif
    </div>
</div>
