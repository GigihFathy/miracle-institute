<?php

namespace App\Observers;

use App\Models\AssessmentAttempt;
use App\Events\AssessmentSubmitted;
use Illuminate\Support\Facades\DB;

class AssessmentAttemptObserver
{
    public function updated(AssessmentAttempt $attempt): void
    {
        if (
            $attempt->wasChanged('submitted_at') &&
            filled($attempt->submitted_at)
        ) {
            DB::afterCommit(function () use ($attempt) {
                event(new AssessmentSubmitted($attempt->id));
            });
        }
    }
}