<?php

namespace App\Livewire\Returns;

use App\Models\ReturnRecord;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $returns = ReturnRecord::with(['inventoryItem', 'receivedBy'])
            ->when($this->search, fn($q) => $q->whereHas('inventoryItem', fn($q2) => $q2->where('item_name', 'like', "%{$this->search}%")))
            ->latest('returned_at')
            ->paginate(15);

        return view('livewire.returns.index', compact('returns'))->layout('layouts.app');
    }
}
