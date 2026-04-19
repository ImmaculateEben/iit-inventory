<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public string $search = '';
    public int $perPage = 10;
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function render()
    {
        $categories = Category::withCount('inventoryItems')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')->paginate($this->perPage);
        return view('livewire.categories.index', compact('categories'))->layout('layouts.app');
    }
}
