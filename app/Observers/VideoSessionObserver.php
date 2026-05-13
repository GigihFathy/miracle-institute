<?php

namespace App\Observers;

use App\Email\Events\VideoSessionScheduled;
use App\Models\VideoSession;
use Illuminate\Support\Facades\DB;

// Video Session ['scheduled', 'ongoing', 'completed', 'cancelled']

class VideoSessionObserver
{
    public function created(VideoSession $videoSession): void
    {
    
        // if (in_array($videoSession->status, ['draft', 'inactive'], true)) {
        //     return;
        // }
        
        DB::afterCommit(function () use ($videoSession) {
            event(new VideoSessionScheduled($videoSession->id));
        });
    }

    public function updated(VideoSession $videoSession): void
    {
        if ($videoSession->wasChanged(['start_at', 'status'])) {
            DB::afterCommit(function () use ($videoSession) {
                event(new VideoSessionScheduled($videoSession->id));
            });
        }
    }
}