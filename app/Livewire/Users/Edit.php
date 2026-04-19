<?php

namespace App\Livewire\Users;

use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use Illuminate\Support\Facades\Hash;
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
            'password' => 'nullable|string|min:8',
            'department_id' => 'required|exists:departments,id',
            'selectedRoles' => 'required|array|min:1',
            'can_view_all_inventory' => 'boolean',
            'accessibleDepartments' => 'array',
            'accessibleCategories' => 'array',
        ];
    }

    public function save()
    {
        $this->validate();

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
            'can_view_all_inventory' => $this->can_view_all_inventory,
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
