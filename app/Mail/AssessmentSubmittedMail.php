<?php

namespace App\Mail;

use App\Models\AssessmentAttempt;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AssessmentSubmittedMail extends BaseLearningMail
{
    public function __construct(public AssessmentAttempt $attempt)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Jawaban assessment diterima');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning.assessment-submitted',
            with: [
                'attempt' => $this->attempt,
                'assessment' => $this->attempt->assessment,
                'course' => $this->attempt->assessment->course,
            ],
        );
    }
}