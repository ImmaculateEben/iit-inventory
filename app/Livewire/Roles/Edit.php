<?php

namespace App\Livewire\Roles;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Edit extends Component
{
    public Role $role;
    public string $name = '';
    public string $description = '';
    public array $selectedPermissions = [];

    public function mount(Role $role): void
    {
        abort_if($role->is_system, 403, 'System roles cannot be modified.');

        $this->role = $role;
        $this->name = $role->name;
        $this->description = $role->description ?? '';
        $this->selectedPermissions = $role->permissions->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:roles,name,' . $this->role->id,
            'description' => 'nullable|string',
            'selectedPermissions' => 'array',
        ];
    }

    public function save()
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_roles_permissions'), 403);

        // Prevent editing system roles
        abort_if($this->role->is_system, 403, 'System roles cannot be modified.');

        $this->validate();
        $this->role->update(['name' => $this->name, 'description' => $this->description]);

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

        $this->role->permissions()->sync($this->selectedPermissions);
        AuditLogger::log('role_updated', Role::class, $this->role->id);
        session()->flash('success', 'Role updated.');
        return $this->redirect(route('roles.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.roles.edit', [
            'permissions' => Permission::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
