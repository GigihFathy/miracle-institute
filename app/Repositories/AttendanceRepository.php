<?php

namespace App\Repositories;

use App\Models\Attendance;

class AttendanceRepository
{
    public function checkIn($userId, $sessionId)
    {
        return Attendance::updateOrCreate(
            [
                'user_id' => $userId,
                'video_session_id' => $sessionId
            ],
            [
                'status' => 'present',
                'check_in_at' => now(),
                'ip_address' => request()->ip()
            ]
        );
    }
}