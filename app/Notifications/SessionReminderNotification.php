<?php

namespace App\Notifications;

use App\Models\VideoSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VideoSession $session)
    {
        $this->onQueue('emails');
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $topic = $this->session->topic;

        return (new MailMessage)
            ->subject("Reminder sesi: {$this->session->title}")
            ->greeting("Halo {$notifiable->full_name},")
            ->line("Sesi {$this->session->title} akan dimulai pada {$this->session->start_at->format('d M Y, H:i')}.")
            ->line("Topic: {$topic?->name}")
            ->action('Buka Course', route('topics.show', $topic->slug))
            ->line('Mohon hadir tepat waktu.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Reminder sesi',
            'message' => "Sesi {$this->session->title} segera dimulai.",
            'url' => route('topics.show', $this->session->topic->slug),
        ];
    }
}