<?php

namespace App\Livewire\Categories;

use App\Models\Category;
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
            'name' => 'required|string|max:100|unique:categories,name',
            'code' => 'required|string|max:20|unique:categories,code',
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
        while (Category::where('code', $code)->exists()) {
            $code = $base . $i;
            $i++;
        }
        return $code;
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['is_active'] = $this->is_active;
        $cat = Category::create($validated);
        AuditLogger::log('category_created', Category::class, $cat->id);
        session()->flash('success', 'Category created.');
        return $this->redirect(route('categories.index'), navigate: true);
    }

    public function render() { return view('livewire.categories.create')->layout('layouts.app'); }
}
