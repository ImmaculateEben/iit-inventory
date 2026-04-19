<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Support\Audit\AuditLogger;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public string $search = '';
    public string|int $perPage = 10;
    public ?int $confirmingDelete = null;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function confirmDelete(int $userId): void
    {
        $this->confirmingDelete = $userId;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = null;
    }

    public function deleteUser(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $user = User::findOrFail($this->confirmingDelete);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            $this->confirmingDelete = null;
            return;
        }

        $userName = $user->name;
        AuditLogger::log('user_deleted', User::class, $user->id);

        $user->roles()->detach();
        $user->accessibleDepartments()->detach();
        $user->accessibleCategories()->detach();
        $user->delete();

        $this->confirmingDelete = null;
        session()->flash('success', "User '{$userName}' has been deleted.");
    }

    public function render()
    {
        $users = User::with(['department', 'roles'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))
            ->orderBy('name')->paginate($this->perPage === 'all' ? PHP_INT_MAX : $this->perPage);
        return view('livewire.users.index', compact('users'))->layout('layouts.app');
    }
}
