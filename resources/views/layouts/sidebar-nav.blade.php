@php
    $currentRoute = request()->route()?->getName() ?? '';
    $user = auth()->user();
    $isAdmin = $user?->isAdmin();

    $navItems = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'permission' => 'view_dashboard', 'icon' => 'home'],
        ['route' => 'inventory.index', 'label' => 'Inventory', 'permission' => 'view_dashboard', 'icon' => 'inventory'],
        ['route' => 'issues.index', 'label' => 'Issues & Assignments', 'permission' => 'issue_items', 'icon' => 'issue'],
        ['route' => 'returns.index', 'label' => 'Returns', 'permission' => 'receive_returns', 'icon' => 'return'],
        ['route' => 'repairs.index', 'label' => 'Repairs', 'permission' => 'manage_repairs', 'icon' => 'repair'],
        ['route' => 'adjustments.index', 'label' => 'Stock Adjustments', 'permission' => 'adjust_stock', 'icon' => 'adjustment'],
    ];

    $adminItems = [
        ['route' => 'departments.index', 'label' => 'Departments', 'permission' => 'manage_departments', 'icon' => 'department'],
        ['route' => 'categories.index', 'label' => 'Categories', 'permission' => 'manage_categories', 'icon' => 'category'],
        ['route' => 'users.index', 'label' => 'Users', 'permission' => 'manage_users', 'icon' => 'users'],
        ['route' => 'roles.index', 'label' => 'Roles & Permissions', 'permission' => 'manage_roles_permissions', 'icon' => 'roles'],
        ['route' => 'audit-log.index', 'label' => 'Audit Log', 'permission' => 'view_audit_log', 'icon' => 'audit'],
    ];

    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
        'inventory' => '<path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />',
        'issue' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />',
        'return' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />',
        'repair' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />',
        'adjustment' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />',
        'department' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />',
        'category' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />',
        'roles' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />',
        'audit' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />',
    ];
@endphp

<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Main</div>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach ($navItems as $item)
                    @if ($isAdmin || ($user && $user->hasPermission($item['permission'])))
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm font-medium leading-6 transition-colors {{ str_starts_with($currentRoute, explode('.', $item['route'])[0]) ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $icons[$item['icon']] !!}</svg>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>

        @php
            $hasAdminAccess = $isAdmin || ($user && collect($adminItems)->contains(fn($item) => $user->hasPermission($item['permission'])));
        @endphp
        <li class="mt-auto">
            <a href="{{ route('settings') }}"
               class="group flex gap-x-3 rounded-md p-2 text-sm font-medium leading-6 transition-colors {{ $currentRoute === 'settings' ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $icons['settings'] !!}</svg>
                Settings
            </a>
        </li>

        @if($hasAdminAccess)
        <li>
            <div class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Administration</div>
            <ul role="list" class="-mx-2 space-y-1">
                @foreach ($adminItems as $item)
                    @if ($isAdmin || ($user && $user->hasPermission($item['permission'])))
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm font-medium leading-6 transition-colors {{ str_starts_with($currentRoute, explode('.', $item['route'])[0]) ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $icons[$item['icon']] !!}</svg>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </li>
        @endif
    </ul>
</nav>
