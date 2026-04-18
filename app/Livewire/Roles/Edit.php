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
        $this->validate();
        $this->role->update(['name' => $this->name, 'description' => $this->description]);
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
