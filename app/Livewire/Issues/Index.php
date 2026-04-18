<?php

namespace App\Livewire\Issues;

use App\Models\IssueRecord;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterType(): void { $this->resetPage(); }

    public function render()
    {
        $issues = IssueRecord::with(['inventoryItem', 'issuedBy'])
            ->when($this->search, fn($q) => $q->whereHas('inventoryItem', fn($q2) => $q2->where('item_name', 'like', "%{$this->search}%")))
            ->when($this->filterType, fn($q) => $q->where('action_type', $this->filterType))
            ->latest('issued_at')
            ->paginate(15);

        return view('livewire.issues.index', compact('issues'))->layout('layouts.app');
    }
}
