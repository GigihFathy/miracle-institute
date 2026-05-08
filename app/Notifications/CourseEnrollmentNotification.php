<?php

namespace App\Notifications;

use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CourseEnrollment $enrollment)
    {
        $this->onQueue('emails');
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $course = $this->enrollment->course;

        return (new MailMessage)
            ->subject("Enrollment berhasil: {$course->title}")
            ->greeting("Halo {$notifiable->full_name},")
            ->line("Kamu berhasil terdaftar di course {$course->title}.")
            ->action('Buka Course', route('courses.show', $course->slug))
            ->line('Silakan mulai belajar dari topic pertama yang tersedia.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Enrollment berhasil',
            'message' => "Kamu terdaftar di course {$this->enrollment->course->title}.",
            'url' => route('courses.show', $this->enrollment->course->slug),
            'course_id' => $this->enrollment->course_id,
            'enrollment_id' => $this->enrollment->id,
        ];
    }
}