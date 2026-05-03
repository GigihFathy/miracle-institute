<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;

class RoleService
{
    public function switchRole(User $user, string $roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        if (!$user->roles->contains('id', $role->id)) {
            throw new \Exception("User tidak memiliki role ini");
        }

        session(['active_role' => $roleName]);

        return true;
    }

    public function getActiveRole($user): string
    {
        return session('active_role')
            ?? $user->roles->pluck('name')->first();
    }
}