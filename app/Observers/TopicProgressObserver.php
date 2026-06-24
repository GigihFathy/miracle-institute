<?php

namespace App\Observers;

use App\Email\Events\ContentCompleted;
use App\Models\TopicProgress;

class TopicProgressObserver
{
    public $afterCommit = true;

    public function updated(TopicProgress $topicProgress): void
    {
        if (
            $topicProgress->wasChanged('status') &&
            $topicProgress->status === 'completed'
        ) {
            event(new ContentCompleted('topic_progress', $topicProgress->id));
        }
    }
}