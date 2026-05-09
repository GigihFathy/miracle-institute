<?php

namespace App\Observers;

use App\Events\VideoSessionCreated;
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
            event(new VideoSessionCreated($videoSession->id));
        });
    }
}