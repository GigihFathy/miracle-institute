<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\WithAdminTableState;
use App\Models\User;
use App\Models\Role;
use Livewire\Component;

class UserIndex extends Component
{
    use WithAdminTableState;

    public $roleFilter = '';
    public $sort = 'latest';

    public function render()
    {
        $query = User::with('roles')

            // SEARCH
            ->when($this->search, function ($q) {
                $q->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })

            // FILTER ROLE
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('name', $this->roleFilter);
                });
            });

        // SORTING
        $query = match ($this->sort) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'email_asc' => $query->orderBy('email'),
            'email_desc' => $query->orderByDesc('email'),
            default => $query->latest(),
        };

        return view('livewire.admin.users.index', [
            'rows' => $query->paginate($this->perPage),
            'roles' => Role::all()->sortBy('name')->values(),
        ])->layout('layouts.admin');
    }
}