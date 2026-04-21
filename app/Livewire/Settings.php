<?php

namespace App\Livewire;

use App\Support\Audit\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Settings extends Component
{
    // Profile tab
    public string $name = '';
    public string $email = '';

    // Password tab
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public string $activeTab = 'profile';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name  = $user->name;
        $this->email = $user->email;
    }

    public function saveProfile(): void
    {
        $user = Auth::user();

        $data = $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $old = $user->only(['name', 'email']);
        $user->update($data);

        AuditLogger::log('profile_updated', 'user', $user->id, $old, $data);

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    public function savePassword(): void
    {
        $user = Auth::user();

        $this->validate([
            'current_password'      => ['required'],
            'new_password'          => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->new_password)]);

        AuditLogger::log('password_changed', 'user', $user->id, 'User changed their own password.');

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('password_success', 'Password changed successfully.');
    }

    public function render()
    {
        return view('livewire.settings')
            ->title('Account Settings');
    }
}
