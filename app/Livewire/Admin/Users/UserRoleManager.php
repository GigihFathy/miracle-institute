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
        abort_unless(auth()->user()?->can('manage_users'), 403);

        $this->user = User::with('roles')->findOrFail($userId);
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('manage_users'), 403);
        abort_if((string) $this->user->id === (string) auth()->id(), 403);

        $roleNames = Role::whereIn('id', $this->selectedRoles)->pluck('name');
        if ($roleNames->contains('admin') && $roleNames->intersect(['student', 'disciples'])->isNotEmpty()) {
            $this->dispatch('toast', type: 'error', message: 'Admin tidak bisa digabung dengan role Student atau Disciples.');
            return;
        }

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