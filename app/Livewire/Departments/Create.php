<?php

namespace App\Livewire\Departments;

use App\Models\Department;
use App\Support\Audit\AuditLogger;
use App\Support\RemembersFormState;
use Livewire\Component;

class Create extends Component
{
    use RemembersFormState;

    protected function formStateFields(): array
    {
        return ['name', 'code', 'description', 'is_active', 'codeManuallyEdited'];
    }

    public string $name = '';
    public string $code = '';
    public string $description = '';
    public bool $is_active = true;
    public bool $codeManuallyEdited = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:departments,name',
            'code' => 'required|string|max:20|unique:departments,code',
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
        while (Department::where('code', $code)->exists()) {
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
        $dept = Department::create($validated);
        AuditLogger::log('department_created', Department::class, $dept->id);
        $this->clearFormState();
        session()->flash('success', 'Department created.');
        return $this->redirect(route('departments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.departments.create')->layout('layouts.app');
    }
}
