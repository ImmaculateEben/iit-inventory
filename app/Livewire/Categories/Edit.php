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
    public bool $codeManuallyEdited = false;

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
        while (Category::where('code', $code)->where('id', '!=', $this->category->id)->exists()) {
            $code = $base . $i;
            $i++;
        }
        return $code;
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
