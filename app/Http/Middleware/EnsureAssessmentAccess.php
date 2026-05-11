<?php

namespace App\Http\Middleware;

use App\Models\Assessment;
use Closure;
use Illuminate\Http\Request;

class EnsureAssessmentAccess
{
    public function handle(Request $request, Closure $next, string $param = 'assessment')
    {
        if (!auth()->check()) {
            abort(401);
        }

        $subject = $request->route($param);

        if ($subject instanceof Assessment) {
            $assessment = $subject;
        } elseif (is_string($subject)) {
            $assessment = Assessment::with('course')->findOrFail($subject);
        } else {
            abort(404);
        }

        $activeRole = session('active_role');

        if (in_array($activeRole, ['admin', 'disciples'], true)) {
            return $next($request);
        }

        $enrolled = $request->user()
            ->courseEnrollments()
            ->where('course_id', $assessment->course_id)
            ->exists();

        if (!$enrolled) {
            abort(403, 'Anda belum terdaftar pada course ini.');
        }

        return $next($request);
    }
}