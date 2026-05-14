<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Certificate $certificate
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sertifikat Siap Diunduh')
            ->view('emails.certificates.issued', [
                'notifiable' => $notifiable,
                'certificate' => $this->certificate,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'certificate_id' => $this->certificate->id,
            'course_id' => $this->certificate->course_id,
            'message' => 'Selamat! Sertifikat kamu sudah terbit dan siap untuk diunduh.',
        ];
    }
}