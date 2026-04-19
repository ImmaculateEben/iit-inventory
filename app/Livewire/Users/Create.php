<?php

namespace App\Livewire\Users;

use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
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

        // Prevent non-admin from assigning admin role
        if (!auth()->user()->isAdmin()) {
            $adminRoleId = Role::where('code', 'admin')->value('id');
            $this->selectedRoles = array_filter($this->selectedRoles, fn($id) => (int) $id !== (int) $adminRoleId);
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'department_id' => $this->department_id,
            'is_active' => $this->is_active,
            'can_view_all_inventory' => auth()->user()->isAdmin() ? $this->can_view_all_inventory : false,
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
            'roles' => Role::when(!auth()->user()->isAdmin(), fn($q) => $q->where('code', '!=', 'admin'))->orderBy('name')->get(),
            'allCategories' => Category::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
