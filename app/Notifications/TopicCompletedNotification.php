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
        return ['mail', 'database'];
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

    public function toArray($notifiable): array
    {
        return [
            'topic_progress_id' => $this->progress->id,
            'topic_id' => $this->progress->topic_id,
            'message' => 'Kamu telah berhasil menyelesaikan satu topik pembelajaran.',
        ];
    }
}