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
    public bool $codeManuallyEdited = false;

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

    public function updatedName($value): void
    {
        if (!$this->codeManuallyEdited && $value !== '') {
            $this->code = $this->generateCode($value);
        }
    }

    public function updatedCode(): void
    {
        $this->codeManuallyEdited = true;
    }

    private function generateCode(string $name): string
    {
        $words = array_filter(preg_split('/[\s\-_]+/', trim($name)));
        if (count($words) > 1) {
            $code = collect($words)->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('');
        } else {
            $code = strtoupper(substr(trim($name), 0, 4));
        }
        $code = substr($code, 0, 20);

        $base = $code;
        $i = 1;
        while (Department::where('code', $code)->where('id', '!=', $this->department->id)->exists()) {
            $code = $base . $i;
            $i++;
        }
        return $code;
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
