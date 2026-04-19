<?php

namespace App\Livewire\Departments;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string|int $perPage = 10;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function render()
    {
        $departments = Department::withCount('inventoryItems')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate($this->perPage === 'all' ? PHP_INT_MAX : $this->perPage);

        return view('livewire.departments.index', compact('departments'))->layout('layouts.app');
    }
}
