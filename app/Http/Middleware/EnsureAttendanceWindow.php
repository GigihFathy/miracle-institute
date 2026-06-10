<?php

namespace App\Http\Middleware;

use App\Models\VideoSession;
use Closure;
use Illuminate\Http\Request;

class EnsureAttendanceWindow
{
    public function handle(Request $request, Closure $next)
    {
        $session = $request->route('session');

        if (is_string($session)) {
            $session = VideoSession::findOrFail($session);
        }

        if (! $session->canJoinAt(now())) {
            abort(403, 'Attendance window is not active.');
        }

        return $next($request);
    }
}
