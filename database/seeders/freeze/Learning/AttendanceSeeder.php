<?php

namespace Database\Seeders\Learning;

use App\Models\Attendance;
use App\Models\VideoSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $sessionOrder = [
            'new-birth',
            'spiritual-disciplines',
            'serving-the-church',
            'hermeneutics-basics',
            'sermon-structure',
            'public-speaking-for-ministry',
        ];

        $patterns = [
            'disciple@example.test' => ['present', 'present', 'present', 'present', 'present', 'present'],
            'student@example.test' => ['present', 'present', 'present', 'present', 'late', 'absent'],
        ];

        $mentor = User::where('email', 'disciple@example.test')->firstOrFail();
        $otherUsers = User::whereIn('email', ['student@example.test'])->get();

        foreach ($sessionOrder as $index => $topicSlug) {
            $session = VideoSession::whereHas('topic', fn ($q) => $q->where('slug', $topicSlug))->first();

            if (!$session) {
                continue;
            }

            // mentor attendance
            Attendance::updateOrCreate(
                ['video_session_id' => $session->id, 'user_id' => $mentor->id],
                [
                    'status' => 'present',
                    'check_in_at' => $session->start_at->copy()->addMinutes(5),
                    'ip_address' => '127.0.0.1',
                ]
            );

            foreach ($otherUsers as $user) {
                $status = $patterns[$user->email][$index] ?? 'absent';

                Attendance::updateOrCreate(
                    ['video_session_id' => $session->id, 'user_id' => $user->id],
                    [
                        'status' => $status,
                        'check_in_at' => in_array($status, ['present', 'late'], true)
                            ? $session->start_at->copy()->addMinutes($status === 'late' ? 15 : 5)
                            : null,
                        'ip_address' => '127.0.0.' . ($index + 10),
                    ]
                );
            }
        }
    }
}