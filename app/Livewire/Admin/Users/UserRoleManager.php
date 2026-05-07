<?php

namespace App\Livewire\Admin\Users;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class UserRoleManager extends Component
{
    public User $user;
    public array $selectedRoles = [];

    public function mount(string $userId): void
    {
        $this->user = User::with('roles')->findOrFail($userId);
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
    }

    public function save(): void
    {
        $this->user->roles()->sync($this->selectedRoles);
        $this->dispatch('toast', type: 'success', message: 'Roles updated.');
    }

    public function render()
    {
        return view('livewire.admin.users.user-role-manager', [
            'roles' => Role::all()->sortBy('name')->values(),
        ])->layout('layouts.admin');
    }
}