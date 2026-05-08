<?php

namespace App\Notifications;

use App\Models\TopicProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TopicCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TopicProgress $progress
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Topik Selesai')
            ->view('emails.topics.completed', [
                'notifiable' => $notifiable,
                'progress' => $this->progress,
            ]);
    }
}