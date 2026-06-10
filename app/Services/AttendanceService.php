<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\VideoSession;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function checkIn(string $userId, string $sessionId): Attendance
    {
        $session = VideoSession::with('topic')->findOrFail($sessionId);
        $now = Carbon::now();

        if (! $session->canJoinAt($now)) {
            throw ValidationException::withMessages([
                'attendance' => 'Sesi Zoom tidak tersedia untuk diikuti saat ini.',
            ]);
        }

        return Attendance::updateOrCreate(
            [
                'video_session_id' => $sessionId,
                'user_id' => $userId,
            ],
            [
                'status' => $session->attendanceStatusAt($now),
                'check_in_at' => $now,
                'ip_address' => request()->ip(),
            ]
        );
    }
}
