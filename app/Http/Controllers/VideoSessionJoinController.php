<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\VideoSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VideoSessionJoinController extends Controller
{
    public function join(Request $request)
    {
        $videoSessionId = (string) $request->route('videoSession', '');
        $videoSession = VideoSession::query()
            ->whereKey($videoSessionId)
            ->first();

        abort_unless($videoSession instanceof VideoSession, 404);

        $user = $request->user();
        $now = now();
        abort_unless($videoSession->canJoinAt($now), 403);

        $activeRole = session('active_role');

        if ($activeRole === 'student') {
            $lockKey = "attendance:{$videoSession->id}:{$user->id}";

            Cache::lock($lockKey, 10)->block(3, function () use ($request, $user, $videoSession, $now) {
                $attendance = Attendance::query()->firstOrNew([
                    'video_session_id' => $videoSession->id,
                    'user_id' => $user->id,
                ]);

                if (! $attendance->exists || ! $attendance->check_in_at) {
                    $attendance->status = $videoSession->attendanceStatusAt($now);
                    $attendance->check_in_at = $now;
                    $attendance->ip_address = $request->ip();
                }

                $attendance->save();
            });
        }

        return redirect()->away($videoSession->zoom_link);
    }
}
