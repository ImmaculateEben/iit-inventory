<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - IIT Inventory</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('iit-logo.svg') }}">
    <link rel="shortcut icon" href="{{ asset('iit-logo.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased" x-data="{ sidebarOpen: false }" x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen)">
    <div class="min-h-full">
        {{-- Mobile sidebar overlay --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/80" @click="sidebarOpen = false"></div>
            <div class="fixed inset-y-0 left-0 z-50 w-72 overflow-y-auto bg-slate-900 px-6 pb-4">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('iit-logo.svg') }}" alt="IIT logo" class="h-9 w-9 rounded-md object-cover">
                        <span class="text-lg font-semibold text-white">IIT Inventory</span>
                    </div>
                    <button type="button" class="-m-2.5 p-2.5 text-gray-400" @click="sidebarOpen = false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                @include('layouts.sidebar-nav')
            </div>
        </div>

        {{-- Desktop sidebar --}}
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-slate-900 px-6 pb-4">
                <div class="flex h-16 shrink-0 items-center gap-3">
                    <img src="{{ asset('iit-logo.svg') }}" alt="IIT logo" class="h-9 w-9 rounded-md object-cover">
                    <span class="text-lg font-semibold text-white">IIT Inventory</span>
                </div>
                @include('layouts.sidebar-nav')
            </div>
        </div>

        {{-- Main content --}}
        <div class="lg:pl-72">
            {{-- Top bar --}}
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>

                <div class="h-6 w-px bg-gray-200 lg:hidden"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center">
                        <h1 class="text-lg font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        {{-- Notifications / Low-Stock Alerts --}}
                        @php
                            $alertService = new \App\Services\DashboardService();
                            $user = auth()->user();
                            $deptIds = $user->getAccessibleDepartmentIds();
                            $catIds = $user->getAccessibleCategoryIds();
                            $headerAlerts = $alertService->getLowStockAlerts($deptIds, $catIds, 5);
                            $alertCount = count($headerAlerts);
                        @endphp
                        <div x-data="{ notifOpen: false }" class="relative" @click.outside="notifOpen = false">
                            <button @click="notifOpen = !notifOpen" class="relative -m-1.5 p-1.5 text-gray-400 hover:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                                @if($alertCount > 0)
                                    <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $alertCount }}</span>
                                @endif
                            </button>
                            <div x-show="notifOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-50 mt-2.5 w-80 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-gray-900/5" style="display: none;">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">Low Stock Alerts</p>
                                </div>
                                @if($alertCount > 0)
                                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                        @foreach($headerAlerts as $alert)
                                            <a href="{{ route('inventory.show', $alert['id']) }}" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                                                <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-50">
                                                    <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $alert['item_name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $alert['department_name'] }}</p>
                                                    <p class="text-xs font-medium text-amber-600">{{ $alert['quantity_available'] }} left (threshold: {{ $alert['effective_threshold'] }})</p>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="border-t border-gray-100 px-4 py-2.5">
                                        <a href="{{ route('dashboard') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800">View all on dashboard &rarr;</a>
                                    </div>
                                @else
                                    <div class="px-4 py-8 text-center">
                                        <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                        <p class="mt-2 text-sm text-gray-500">All stocked up!</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200"></div>

                        {{-- Profile dropdown --}}
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <button type="button" class="-m-1.5 flex items-center p-1.5" @click="open = !open">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-medium text-white">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-3 text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'User' }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                                </span>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5" style="display: none;">
                                <div class="px-4 py-2 text-xs text-gray-500">
                                    {{ auth()->user()->email ?? '' }}
                                </div>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Page content --}}
            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    {{-- Flash messages --}}
                    @if (session('success'))
                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                            <div class="flex items-center">
                                <svg class="mr-2 h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
