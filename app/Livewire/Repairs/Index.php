<?php

namespace App\Livewire\Repairs;

use App\Models\RepairRecord;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public int $perPage = 10;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function render()
    {
        $user = auth()->user();
        $deptIds = $user->getAccessibleDepartmentIds();
        $catIds = $user->getAccessibleCategoryIds();

        $repairs = RepairRecord::with(['inventoryItem', 'createdBy'])
            ->when($deptIds !== null, fn($q) => $q->whereHas('inventoryItem', fn($iq) => $iq->whereIn('department_id', $deptIds)))
            ->when($catIds !== null, fn($q) => $q->whereHas('inventoryItem', fn($iq) => $iq->whereIn('category_id', $catIds)))
            ->when($this->search, fn($q) => $q->whereHas('inventoryItem', fn($q2) => $q2->where('item_name', 'like', "%{$this->search}%")->orWhere('item_code', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.repairs.index', compact('repairs'))->layout('layouts.app');
    }
}
