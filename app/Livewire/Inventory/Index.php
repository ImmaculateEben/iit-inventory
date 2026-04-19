<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';
    public string $filterCategory = '';
    public string $filterDepartment = '';
    public string $sortBy = 'item_name';
    public string $sortDir = 'asc';

    protected array $allowedSorts = ['item_name', 'item_code', 'item_type', 'category_id', 'department_id', 'quantity_in_stock', 'created_at'];

    protected $queryString = ['search', 'filterType', 'filterCategory', 'filterDepartment'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (!in_array($column, $this->allowedSorts)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
    }

    public function render()
    {
        $user = auth()->user();
        $deptIds = $user->getAccessibleDepartmentIds();
        $catIds = $user->getAccessibleCategoryIds();

        $query = InventoryItem::with(['category', 'department'])
            ->when($deptIds !== null, fn($q) => $q->whereIn('department_id', $deptIds))
            ->when($catIds !== null, fn($q) => $q->whereIn('category_id', $catIds))
            ->when($this->search, fn($q) => $q->where(function ($sq) {
                $sq->where('item_name', 'like', "%{$this->search}%")
                   ->orWhere('item_code', 'like', "%{$this->search}%");
            }))
            ->when($this->filterType, fn($q) => $q->where('item_type', $this->filterType))
            ->when($this->filterCategory && ($catIds === null || in_array($this->filterCategory, $catIds)), fn($q) => $q->where('category_id', $this->filterCategory))
            ->when($this->filterDepartment && ($deptIds === null || in_array($this->filterDepartment, $deptIds)), fn($q) => $q->where('department_id', $this->filterDepartment))
            ->orderBy(in_array($this->sortBy, $this->allowedSorts) ? $this->sortBy : 'item_name', $this->sortDir);

        // Scope filter dropdowns to accessible items
        $departmentQuery = \App\Models\Department::orderBy('name');
        if ($deptIds !== null) {
            $departmentQuery->whereIn('id', $deptIds);
        }

        $categoryQuery = \App\Models\Category::orderBy('name');
        if ($catIds !== null) {
            $categoryQuery->whereIn('id', $catIds);
        }

        return view('livewire.inventory.index', [
            'items' => $query->paginate(15),
            'categories' => $categoryQuery->get(),
            'departments' => $departmentQuery->get(),
        ])->layout('layouts.app');
    }
}
