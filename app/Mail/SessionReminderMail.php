<?php

namespace App\Mail;

use App\Models\VideoSession;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SessionReminderMail extends BaseLearningMail
{
    public function __construct(public VideoSession $session)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reminder sesi: ' . $this->session->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning.session-reminder',
            with: [
                'session' => $this->session,
                'topic' => $this->session->topic,
                'course' => $this->session->topic->course,
                'url' => route('topics.show', $this->session->topic->slug),
            ],
        );
    }
}