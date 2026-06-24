<?php

namespace App\Email\Listeners;

use App\Email\Events\AssessmentSubmissionProcessed;
use App\Models\AssessmentAttempt;
use App\Notifications\AssessmentSubmissionReceiptNotification;

class SendAssessmentSubmissionNotification 
{
    public function handle(AssessmentSubmissionProcessed $event): void
    {
        return;

        $attempt = AssessmentAttempt::with(['user', 'assessment'])
            ->findOrFail($event->attemptId);

        $attempt->user->notify(
            new AssessmentSubmissionReceiptNotification($attempt)
        );
    }
}