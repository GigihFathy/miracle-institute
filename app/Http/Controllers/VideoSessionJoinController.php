<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\VideoSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VideoSessionJoinController extends Controller
{
    public function join(Request $request, VideoSession $videoSession)
    {
        $user = $request->user();
        $now = now();

        abort_unless(in_array($videoSession->status, ['scheduled', 'ongoing'], true), 403);
        abort_unless($now->betweenIncluded($videoSession->start_at, $videoSession->end_at), 403);

        $activeRole = session('active_role');

        if ($activeRole === 'student') {
            $lockKey = "attendance:{$videoSession->id}:{$user->id}";

            Cache::lock($lockKey, 10)->block(3, function () use ($request, $user, $videoSession, $now) {
                Attendance::firstOrCreate(
                    [
                        'video_session_id' => $videoSession->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'status' => $now->lte($videoSession->start_at->copy()->addMinutes(45))
                            ? 'present'
                            : 'late',
                        'check_in_at' => $now,
                        'clock_out_at' => null,
                        'ip_address' => $request->ip(),
                    ]
                );
            });
        }

        return redirect()->away($videoSession->zoom_link);
    }
}