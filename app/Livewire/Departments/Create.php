<?php

namespace App\Livewire\Departments;

use App\Models\Department;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:departments,name',
            'code' => 'required|string|max:20|unique:departments,code',
            'description' => 'nullable|string',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['is_active'] = $this->is_active;
        $dept = Department::create($validated);
        AuditLogger::log('department_created', Department::class, $dept->id);
        session()->flash('success', 'Department created.');
        return $this->redirect(route('departments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.departments.create')->layout('layouts.app');
    }
}
