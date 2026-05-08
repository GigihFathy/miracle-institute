<?php

namespace App\Listeners;

use App\Events\AssessmentSubmitted;
use App\Models\AssessmentAttempt;
use App\Notifications\AssessmentSubmissionReceiptNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAssessmentSubmissionReceiptEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'emails';

    public function handle(AssessmentSubmitted $event): void
    {
        $attempt = AssessmentAttempt::query()
            ->with([
                'user',
                'assessment',
                'assessment.course',
            ])
            ->findOrFail($event->attemptId);

        $attempt->user->notify(
            new AssessmentSubmissionReceiptNotification($attempt)
        );
    }
}