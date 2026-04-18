<?php

namespace App\Livewire\AuditLog;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public string $search = '';
    public string $filterAction = '';
    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $logs = AuditLog::with('user')
            ->when($this->search, fn($q) => $q->where('action_code', 'like', "%{$this->search}%")->orWhere('summary', 'like', "%{$this->search}%")->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterAction, fn($q) => $q->where('action_code', $this->filterAction))
            ->latest('created_at')->paginate(25);

        $actions = AuditLog::select('action_code')->distinct()->orderBy('action_code')->pluck('action_code');

        return view('livewire.audit-log.index', compact('logs', 'actions'))->layout('layouts.app');
    }
}
