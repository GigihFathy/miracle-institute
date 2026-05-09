<?php

namespace App\Listeners;

use App\Events\AssessmentSubmitted;
use App\Models\AssessmentAttempt;
use App\Notifications\AssessmentSubmissionReceiptNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAssessmentSubmissionReceiptEmail implements ShouldQueue
{
    public function handle(AssessmentSubmitted $event): void
    {
        $attempt = AssessmentAttempt::with([
            'user',
            'assessment'
        ])->findOrFail($event->attemptId);

        $attempt->user->notify(
            new AssessmentSubmissionReceiptNotification($attempt)
        );
    }
}