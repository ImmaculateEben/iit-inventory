<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public string $search = '';
    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $categories = Category::withCount('inventoryItems')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')->paginate(15);
        return view('livewire.categories.index', compact('categories'))->layout('layouts.app');
    }
}
