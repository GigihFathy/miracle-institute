<?php

namespace App\Observers;

use App\Email\Events\AssessmentSubmissionProcessed;
use App\Models\AssessmentAttempt;

class AssessmentAttemptObserver
{
    public $afterCommit = true;

    public function updated(AssessmentAttempt $attempt): void
    {
        if (
            $attempt->wasChanged('submitted_at') &&
            filled($attempt->submitted_at)
        ) {
            event(new AssessmentSubmissionProcessed(
                $attempt->id,
                $attempt->passed ?? false
            ));
        }
    }
}