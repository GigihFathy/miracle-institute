<?php

namespace App\Http\Middleware;

use App\Models\Topic;
use App\Services\LearningAccessRequirementService;
use Closure;
use Illuminate\Http\Request;

class EnsureTopicAccess
{
    public function handle(Request $request, Closure $next, string $param = 'topic')
    {
        if (!auth()->check()) {
            abort(401);
        }

        $subject = $request->route($param);

        if ($subject instanceof Topic) {
            $topic = $subject;
        } elseif (is_string($subject)) {
            $topic = Topic::where('slug', $subject)->firstOrFail();
        } else {
            abort(404);
        }

        $activeRole = session('active_role');

        if (in_array($activeRole, ['admin', 'disciples'], true)) {
            return $next($request);
        }

        $requirements = app(LearningAccessRequirementService::class);

        if (! $requirements->topicIsPublished($topic) || ! $requirements->topicHasStudentAccessRequirements($topic)) {
            abort(403, 'Topik ini belum aktif untuk murid.');
        }

        $enrolled = $request->user()
            ->courseEnrollments()
            ->where('course_id', $topic->course_id)
            ->exists();

        if (!$enrolled) {
            abort(403, 'Anda belum terdaftar pada course ini.');
        }

        return $next($request);
    }
}
