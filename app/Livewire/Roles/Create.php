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
        $this->validate();

        $code = strtolower(str_replace(' ', '_', trim($this->name)));

        $role = Role::create([
            'name' => $this->name,
            'code' => $code,
            'description' => $this->description ?: null,
            'is_system' => false,
        ]);

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
