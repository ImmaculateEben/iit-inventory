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
        $code = '';
        if (count($words) > 1) {
            $charIndex = 0;
            while (strlen($code) < 4) {
                $added = false;
                foreach ($words as $word) {
                    if ($charIndex < strlen($word)) {
                        $code .= strtoupper($word[$charIndex]);
                        $added = true;
                        if (strlen($code) >= 4) break;
                    }
                }
                if (!$added) break;
                $charIndex++;
            }
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
