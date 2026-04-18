<?php

namespace App\Livewire\Users;

use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return ['name', 'email', 'department_id', 'selectedRoles', 'is_active', 'can_view_all_inventory', 'accessibleDepartments', 'accessibleCategories'];
    }

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $department_id = '';
    public array $selectedRoles = [];
    public bool $is_active = true;
    public bool $can_view_all_inventory = false;
    public array $accessibleDepartments = [];
    public array $accessibleCategories = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
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
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'department_id' => $this->department_id,
            'is_active' => $this->is_active,
            'can_view_all_inventory' => $this->can_view_all_inventory,
        ]);
        $user->roles()->sync($this->selectedRoles);

        if (!$this->can_view_all_inventory) {
            $user->accessibleDepartments()->sync($this->accessibleDepartments);
            $user->accessibleCategories()->sync($this->accessibleCategories);
        }

        AuditLogger::log('user_created', User::class, $user->id);
        $this->clearFormState();
        session()->flash('success', 'User created.');
        return $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.users.create', [
            'departments' => Department::orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
            'allCategories' => Category::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
