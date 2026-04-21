<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Account Settings</h2>
        <p class="mt-1 text-sm text-gray-500">Manage your profile information and password.</p>
    </div>

    {{-- Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex gap-x-6">
            <button wire:click="$set('activeTab', 'profile')"
                class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'profile' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Profile
            </button>
            <button wire:click="$set('activeTab', 'password')"
                class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === 'password' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Change Password
            </button>
        </nav>
    </div>

    {{-- Profile Tab --}}
    @if($activeTab === 'profile')
    <div class="max-w-lg">
        @if(session('profile_success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200 flex items-center gap-2"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <svg class="h-4 w-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                {{ session('profile_success') }}
            </div>
        @endif

        <form wire:submit="saveProfile">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100 space-y-5">
                {{-- Avatar initial --}}
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->roles->first()?->name ?? 'No role assigned' }}</p>
                        @if(auth()->user()->department)
                            <p class="text-xs text-gray-400">{{ auth()->user()->department->name }}</p>
                        @endif
                    </div>
                </div>

                <hr class="border-gray-100">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text"
                           class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                    <input wire:model="email" type="email"
                           class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        Save Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endif

    {{-- Password Tab --}}
    @if($activeTab === 'password')
    <div class="max-w-lg">
        @if(session('password_success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200 flex items-center gap-2"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <svg class="h-4 w-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                {{ session('password_success') }}
            </div>
        @endif

        <form wire:submit="savePassword">
            <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-100 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password <span class="text-red-500">*</span></label>
                    <input wire:model="current_password" type="password" autocomplete="current-password"
                           class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password <span class="text-red-500">*</span></label>
                    <input wire:model="new_password" type="password" autocomplete="new-password"
                           class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-400">Minimum 8 characters.</p>
                    @error('new_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password <span class="text-red-500">*</span></label>
                    <input wire:model="new_password_confirmation" type="password" autocomplete="new-password"
                           class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('new_password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                        Update Password
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs text-amber-700">
                <strong>Note:</strong> This is a closed system. If you are locked out, contact your system administrator to reset your account.
            </p>
        </div>
    </div>
    @endif
</div>
