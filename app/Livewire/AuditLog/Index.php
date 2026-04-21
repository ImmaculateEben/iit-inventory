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
    public string|int $perPage = 25;
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function render()
    {
        $user = auth()->user();

        $query = AuditLog::with('user');

        // Non-admin users can only see their own actions
        if (!$user->isAdmin()) {
            $query->where('actor_user_id', $user->id);
        }

        $logs = $query
            ->when($this->search, fn($q) => $q->where(function ($sq) {
                $sq->where('action_code', 'like', "%{$this->search}%")
                   ->orWhere('summary', 'like', "%{$this->search}%")
                   ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterAction, fn($q) => $q->where('action_code', $this->filterAction))
            ->latest('created_at')->paginate($this->perPage === 'all' ? PHP_INT_MAX : min((int) $this->perPage, 250));

        $actionsQuery = AuditLog::select('action_code')->distinct()->orderBy('action_code');
        if (!$user->isAdmin()) {
            $actionsQuery->where('actor_user_id', $user->id);
        }
        $actions = $actionsQuery->pluck('action_code');

        return view('livewire.audit-log.index', compact('logs', 'actions'))->layout('layouts.app');
    }
}
