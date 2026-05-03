<?php

namespace App\Livewire\Shared;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RoleSwitcher extends Component
{
    public $activeRole;
    public $roles = [];

    protected $listeners = [
        'roleSwitched' => '$refresh',
    ];

    
    public function mount(RoleService $roleService)
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        $this->roles = $user
            ? $user->roles()->select('roles.id', 'roles.name', 'roles.label')->get()->toArray()
            : [];

        $this->activeRole = $roleService->getActiveRole($user);
    }

    public function switchRole(RoleService $roleService, string $roleName)
    {
        $user = Auth::user();

        $roleService->switchRole($user, $roleName);

        $this->activeRole = $roleName;

        return match ($roleName) {
            'admin' => redirect()->route('admin.dashboard'),
            'disciples' => redirect()->route('mentor.dashboard'),
            'student' => redirect()->route('explore.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
    

    public function render()
    {
        return view('livewire.shared.role-switcher');
    }
}