<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectAdminFromUserPages
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $activeRole = session('active_role');

        if (!$activeRole) {
            $activeRole = auth()->user()->loadMissing('roles')->roles->pluck('name')->first();
        }

        if ($activeRole === 'admin') {
            return redirect()->to(localized_route('admin.dashboard'));
        }

        return $next($request);
    }
}
