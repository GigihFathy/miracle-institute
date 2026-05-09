<?php

namespace Database\Seeders\Learning;

use App\Models\VideoSession;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class VideoSessionSeeder extends Seeder
{
    public function run(): void
    {
        $topicOrder = [
            'new-birth',
            'spiritual-disciplines',
            'serving-the-church',
            'hermeneutics-basics',
            'sermon-structure',
            'public-speaking-for-ministry',
        ];

        foreach ($topicOrder as $index => $slug) {
            $topic = Topic::where('slug', $slug)->firstOrFail();
            $startAt = now()->addDays(($index + 1) * 2)->setTime(19, 0);
            $endAt = $startAt->copy()->addHour();

            VideoSession::factory()->create([
                'topic_id' => $topic->id,
                'title' => 'Session for ' . $topic->name,
                'zoom_link' => 'https://zoom.us/j/1234567890?topic=' . $slug,
                'record_link' => 'https://youtube.com/watch?v=record-' . $slug,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'reminder_sent_at' => $startAt->copy()->subHour(),
                'status' => 'scheduled',
            ]);
        }
    }
}