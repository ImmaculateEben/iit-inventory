<?php

namespace App\Livewire\Users;

use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Edit extends Component
{
    public User $user;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $department_id = '';
    public array $selectedRoles = [];
    public bool $is_active = true;
    public bool $can_view_all_inventory = false;
    public array $accessibleDepartments = [];
    public array $accessibleCategories = [];

    public function mount(User $user): void
    {
        $this->user = $user;

        // Prevent non-admin from editing admin accounts
        if (!auth()->user()->isAdmin() && $user->isAdmin()) {
            abort(403, 'Only administrators can edit admin accounts.');
        }

        $this->fill($user->only(['name', 'email', 'is_active', 'can_view_all_inventory']));
        $this->department_id = (string) $user->department_id;
        $this->selectedRoles = $user->roles->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->accessibleDepartments = $user->accessibleDepartments->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->accessibleCategories = $user->accessibleCategories->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'department_id' => 'required|exists:departments,id',
            'selectedRoles' => 'required|array|min:1',
            'can_view_all_inventory' => 'boolean',
            'accessibleDepartments' => 'array',
            'accessibleCategories' => 'array',
        ];
    }

    public function save()
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_users'), 403);

        $this->validate();

        // Prevent non-admin from editing admin accounts
        if (!auth()->user()->isAdmin() && $this->user->isAdmin()) {
            abort(403, 'Only administrators can edit admin accounts.');
        }

        // Prevent non-admin from assigning admin role
        if (!auth()->user()->isAdmin()) {
            $adminRoleId = Role::where('code', 'admin')->value('id');
            $this->selectedRoles = array_filter($this->selectedRoles, fn($id) => (int) $id !== (int) $adminRoleId);
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->department_id,
            'is_active' => $this->is_active,
            'can_view_all_inventory' => auth()->user()->isAdmin() ? $this->can_view_all_inventory : $this->user->can_view_all_inventory,
        ];
        if ($this->password) $data['password'] = Hash::make($this->password);
        $this->user->update($data);
        $this->user->roles()->sync($this->selectedRoles);

        if ($this->can_view_all_inventory) {
            $this->user->accessibleDepartments()->detach();
            $this->user->accessibleCategories()->detach();
        } else {
            $this->user->accessibleDepartments()->sync($this->accessibleDepartments);
            $this->user->accessibleCategories()->sync($this->accessibleCategories);
        }

        AuditLogger::log('user_updated', User::class, $this->user->id);
        session()->flash('success', 'User updated.');
        return $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.users.edit', [
            'departments' => Department::orderBy('name')->get(),
            'roles' => Role::when(!auth()->user()->isAdmin(), fn($q) => $q->where('code', '!=', 'admin'))->orderBy('name')->get(),
            'allCategories' => Category::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
