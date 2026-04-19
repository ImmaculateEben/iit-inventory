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
        $words = array_values(array_filter(preg_split('/[\s\-_]+/', trim($name))));
        $count = count($words);

        if ($count >= 4) {
            $code = collect($words)->map(fn($w) => strtoupper($w[0]))->implode('');
        } elseif ($count === 3) {
            $code = strtoupper($words[0][0]) . strtoupper($words[1][0]) . strtoupper(substr($words[2], 0, 2));
        } elseif ($count === 2) {
            $code = strtoupper(substr($words[0], 0, 2)) . strtoupper(substr($words[1], 0, 2));
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
        abort_unless(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_departments'), 403);

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
