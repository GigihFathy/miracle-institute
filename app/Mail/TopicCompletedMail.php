<?php

namespace App\Mail;

use App\Models\TopicProgress;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TopicCompletedMail extends BaseLearningMail
{
    public function __construct(public TopicProgress $topicProgress)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Topik selesai: ' . $this->topicProgress->topic->name);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.learning.topic-completed',
            with: [
                'topicProgress' => $this->topicProgress,
                'topic' => $this->topicProgress->topic,
                'course' => $this->topicProgress->topic->course,
                'url' => route('topics.show', $this->topicProgress->topic->slug),
            ],
        );
    }
}