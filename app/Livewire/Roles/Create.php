<?php

namespace App\Livewire\Roles;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return ['name', 'description', 'selectedPermissions'];
    }

    public string $name = '';
    public string $description = '';
    public array $selectedPermissions = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string',
            'selectedPermissions' => 'array',
        ];
    }

    public function save()
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_roles_permissions'), 403);

        $this->validate();

        $code = strtolower(str_replace(' ', '_', trim($this->name)));

        $role = Role::create([
            'name' => $this->name,
            'code' => $code,
            'description' => $this->description ?: null,
            'is_system' => false,
        ]);

        // Non-admin users can only assign permissions they themselves hold
        if (!auth()->user()->isAdmin()) {
            $userPermissionIds = auth()->user()->roles()
                ->with('permissions')
                ->get()
                ->flatMap(fn($role) => $role->permissions)
                ->pluck('id')
                ->toArray();
            $this->selectedPermissions = array_values(array_intersect(
                array_map('intval', $this->selectedPermissions),
                $userPermissionIds
            ));
        }

        $role->permissions()->sync($this->selectedPermissions);

        AuditLogger::log('role_created', Role::class, $role->id);
        $this->clearFormState();
        session()->flash('success', 'Role created successfully.');
        return $this->redirect(route('roles.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.roles.create', [
            'permissions' => Permission::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
