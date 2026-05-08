<?php

namespace App\Mail;

use App\Models\Assessment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AssessmentPublishedMail extends BaseLearningMail
{
    public function __construct(public Assessment $assessment)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Assessment baru tersedia: ' . $this->assessment->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning.assessment-published',
            with: [
                'assessment' => $this->assessment,
                'course' => $this->assessment->course,
                'url' => route('courses.show', $this->assessment->course->slug),
            ],
        );
    }
}