<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - IIT Inventory</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    <div class="flex min-h-full">
        {{-- Left panel - branding --}}
        <div class="hidden lg:flex lg:w-1/2 lg:flex-col lg:justify-center lg:bg-slate-900 lg:px-12">
            <div class="mx-auto max-w-md">
                <div class="flex items-center gap-3 mb-8">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">IIT Inventory</span>
                </div>
                <h1 class="text-4xl font-bold text-white leading-tight mb-4">
                    Streamlined Inventory Management
                </h1>
                <p class="text-lg text-slate-300 leading-relaxed">
                    Track assets, manage stock, handle requests, and maintain complete audit trails - all in one place.
                </p>
                <div class="mt-10 grid grid-cols-2 gap-6">
                    <div class="rounded-lg bg-slate-800/50 p-4">
                        <div class="text-2xl font-bold text-blue-400">100%</div>
                        <div class="text-sm text-slate-400 mt-1">Asset Tracking</div>
                    </div>
                    <div class="rounded-lg bg-slate-800/50 p-4">
                        <div class="text-2xl font-bold text-emerald-400">Real-time</div>
                        <div class="text-sm text-slate-400 mt-1">Stock Updates</div>
                    </div>
                    <div class="rounded-lg bg-slate-800/50 p-4">
                        <div class="text-2xl font-bold text-amber-400">Full</div>
                        <div class="text-sm text-slate-400 mt-1">Audit Trail</div>
                    </div>
                    <div class="rounded-lg bg-slate-800/50 p-4">
                        <div class="text-2xl font-bold text-purple-400">Smart</div>
                        <div class="text-sm text-slate-400 mt-1">Workflows</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right panel - login form --}}
        <div class="flex w-full flex-col justify-center px-4 py-12 sm:px-6 lg:w-1/2 lg:px-12 bg-gray-50">
            <div class="mx-auto w-full max-w-sm">
                <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">IIT Inventory</span>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h2>
                    <p class="text-sm text-gray-500 mb-8">Sign in to your account to continue</p>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                            <input id="email" name="email" type="email" required autofocus
                                   value="{{ old('email') }}"
                                   class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition"
                                   placeholder="you@iit.edu">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                            <input id="password" name="password" type="password" required
                                   class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition"
                                   placeholder="••••••••">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500/20">
                                <span class="text-sm text-gray-600">Remember me</span>
                            </label>
                        </div>
                        <button type="submit"
                                class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                            Sign in
                        </button>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    IIT Inventory Management System &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
