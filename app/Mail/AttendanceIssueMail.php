<?php

namespace App\Mail;

use App\Models\Attendance;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AttendanceIssueMail extends BaseLearningMail
{
    public function __construct(public Attendance $attendance, public string $message)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Kendala presensi: ' . $this->attendance->videoSession->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning.attendance-issue',
            with: [
                'attendance' => $this->attendance,
                'messageBody' => $this->message,
            ],
        );
    }
}