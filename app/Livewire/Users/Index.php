<?php

namespace App\Livewire\Users;

use App\Models\User;
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
        $users = User::with(['department', 'roles'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')->paginate($this->perPage === 'all' ? PHP_INT_MAX : $this->perPage);
        return view('livewire.users.index', compact('users'))->layout('layouts.app');
    }
}
