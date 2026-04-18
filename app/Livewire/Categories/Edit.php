<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use App\Support\Audit\AuditLogger;
use Livewire\Component;

class Edit extends Component
{
    public Category $category;
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public bool $is_active = true;

    public function mount(Category $category): void
    {
        $this->category = $category;
        $this->fill($category->only(['name', 'code', 'description', 'is_active']));
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:categories,name,' . $this->category->id,
            'code' => 'required|string|max:20|unique:categories,code,' . $this->category->id,
            'description' => 'nullable|string',
        ];
    }

    public function save()
    {
        $validated = $this->validate();
        $validated['is_active'] = $this->is_active;
        $this->category->update($validated);
        AuditLogger::log('category_updated', Category::class, $this->category->id);
        session()->flash('success', 'Category updated.');
        return $this->redirect(route('categories.index'), navigate: true);
    }

    public function render() { return view('livewire.categories.edit')->layout('layouts.app'); }
}
