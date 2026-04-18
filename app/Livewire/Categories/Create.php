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

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:categories,name',
            'code' => 'required|string|max:20|unique:categories,code',
            'description' => 'nullable|string',
        ];
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
