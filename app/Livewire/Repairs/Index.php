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

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $repairs = RepairRecord::with(['inventoryItem', 'createdBy'])
            ->when($this->search, fn($q) => $q->whereHas('inventoryItem', fn($q2) => $q2->where('item_name', 'like', "%{$this->search}%")->orWhere('item_code', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);

        return view('livewire.repairs.index', compact('repairs'))->layout('layouts.app');
    }
}
