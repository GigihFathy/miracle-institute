<?php

namespace App\Observers;

use App\Email\Events\VideoSessionScheduled;
use App\Models\VideoSession;

class VideoSessionObserver
{
    public $afterCommit = true;

    public function created(VideoSession $videoSession): void
    {
        event(new VideoSessionScheduled($videoSession->id));
    }

    public function updated(VideoSession $videoSession): void
    {
        if ($videoSession->wasChanged(['start_at', 'status'])) {
            event(new VideoSessionScheduled($videoSession->id));
        }
    }
}