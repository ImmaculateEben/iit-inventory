<?php

namespace App\Livewire\Departments;

use App\Models\Department;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Edit extends Component
{
    public Department $department;
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public bool $is_active = true;

    public function mount(Department $department): void
    {
        $this->department = $department;
        $this->fill($department->only(['name', 'code', 'description', 'is_active']));
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:departments,name,' . $this->department->id,
            'code' => 'required|string|max:20|unique:departments,code,' . $this->department->id,
            'description' => 'nullable|string',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['is_active'] = $this->is_active;
        $this->department->update($validated);
        AuditLogger::log('department_updated', Department::class, $this->department->id);
        session()->flash('success', 'Department updated.');
        return $this->redirect(route('departments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.departments.edit')->layout('layouts.app');
    }
}
