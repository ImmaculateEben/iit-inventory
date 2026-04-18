<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $roles = Role::withCount('users')->with('permissions')->orderBy('name')->get();
        return view('livewire.roles.index', compact('roles'))->layout('layouts.app');
    }
}
