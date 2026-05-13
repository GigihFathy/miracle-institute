<?php

namespace App\Email\Listeners;

use App\Email\Events\AssessmentSubmissionProcessed;
use App\Models\AssessmentAttempt;
use App\Notifications\AssessmentSubmissionReceiptNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAssessmentSubmissionNotification implements ShouldQueue
{
    public function handle(AssessmentSubmissionProcessed $event): void
    {
        $attempt = AssessmentAttempt::with(['user', 'assessment'])
            ->findOrFail($event->attemptId);

        $attempt->user->notify(
            new AssessmentSubmissionReceiptNotification($attempt)
        );
    }
}
