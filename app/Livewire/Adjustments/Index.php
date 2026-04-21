<?php

namespace App\Livewire\Adjustments;

use App\Models\StockAdjustment;
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
        $user = auth()->user();
        $deptIds = $user->getAccessibleDepartmentIds();
        $catIds = $user->getAccessibleCategoryIds();

        $adjustments = StockAdjustment::with(['inventoryItem', 'performedBy'])
            ->when($deptIds !== null, fn($q) => $q->whereHas('inventoryItem', fn($iq) => $iq->whereIn('department_id', $deptIds)))
            ->when($catIds !== null, fn($q) => $q->whereHas('inventoryItem', fn($iq) => $iq->whereIn('category_id', $catIds)))
            ->when($this->search, fn($q) => $q->whereHas('inventoryItem', fn($q2) => $q2->where('item_name', 'like', "%{$this->search}%")))
            ->latest()->paginate($this->perPage === 'all' ? PHP_INT_MAX : min((int) $this->perPage, 250));
        return view('livewire.adjustments.index', compact('adjustments'))->layout('layouts.app');
    }
}
