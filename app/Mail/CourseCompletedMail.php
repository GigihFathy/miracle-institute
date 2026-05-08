<?php

namespace App\Mail;

use App\Models\CourseEnrollment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CourseCompletedMail extends BaseLearningMail
{
    public function __construct(public CourseEnrollment $enrollment)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Course selesai: ' . $this->enrollment->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning.course-completed',
            with: [
                'enrollment' => $this->enrollment,
                'course' => $this->enrollment->course,
                'user' => $this->enrollment->user,
                'url' => route('courses.show', $this->enrollment->course->slug),
            ],
        );
    }
}