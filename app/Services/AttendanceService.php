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

        $windowStart = $session->start_at->copy()->subMinutes(15);
        $windowEnd = $session->end_at->copy();

        if ($now->lt($windowStart)) {
            throw ValidationException::withMessages([
                'attendance' => 'Absensi belum dibuka.',
            ]);
        }

        if ($now->gt($windowEnd)) {
            throw ValidationException::withMessages([
                'attendance' => 'Absensi sudah ditutup.',
            ]);
        }

        return Attendance::updateOrCreate(
            [
                'video_session_id' => $sessionId,
                'user_id' => $userId,
            ],
            [
                'status' => $now->gt($session->start_at) ? 'late' : 'present',
                'check_in_at' => $now,
                'ip_address' => request()->ip(),
            ]
        );
    }
}