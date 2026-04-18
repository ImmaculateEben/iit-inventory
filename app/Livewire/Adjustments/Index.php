<?php

namespace App\Livewire\Adjustments;

use App\Models\StockAdjustment;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public string $search = '';
    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $adjustments = StockAdjustment::with(['inventoryItem', 'adjustedBy'])
            ->when($this->search, fn($q) => $q->whereHas('inventoryItem', fn($q2) => $q2->where('item_name', 'like', "%{$this->search}%")))
            ->latest()->paginate(15);
        return view('livewire.adjustments.index', compact('adjustments'))->layout('layouts.app');
    }
}
