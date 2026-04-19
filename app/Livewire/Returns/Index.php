<?php

namespace App\Livewire\Returns;

use App\Models\ReturnRecord;
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
        $user = auth()->user();
        $deptIds = $user->getAccessibleDepartmentIds();
        $catIds = $user->getAccessibleCategoryIds();

        $returns = ReturnRecord::with(['inventoryItem', 'receivedBy'])
            ->when($deptIds !== null, fn($q) => $q->whereHas('inventoryItem', fn($iq) => $iq->whereIn('department_id', $deptIds)))
            ->when($catIds !== null, fn($q) => $q->whereHas('inventoryItem', fn($iq) => $iq->whereIn('category_id', $catIds)))
            ->when($this->search, fn($q) => $q->whereHas('inventoryItem', fn($q2) => $q2->where('item_name', 'like', "%{$this->search}%")))
            ->latest('returned_at')
            ->paginate($this->perPage);

        return view('livewire.returns.index', compact('returns'))->layout('layouts.app');
    }
}
