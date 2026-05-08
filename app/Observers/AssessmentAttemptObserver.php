<?php

namespace App\Observers;

use App\Models\AssessmentAttempt;
use App\Services\LearningNotificationService;

class AssessmentAttemptObserver
{
    public function __construct(protected LearningNotificationService $notifier) {}

    public function updated(AssessmentAttempt $attempt): void
    {
        if (! $attempt->wasChanged('submitted_at')) {
            return;
        }

        if (! $attempt->submitted_at) {
            return;
        }

        $attempt->loadMissing(['user', 'assessment.course']);
        $this->notifier->sendAssessmentSubmitted($attempt);
    }
}