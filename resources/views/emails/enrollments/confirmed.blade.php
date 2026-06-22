@component('emails.layouts.learning', [
    'title' => 'Enrollment Berhasil',
])
    <p style="margin:0 0 16px 0;">
        Halo <strong>{{ $notifiable->name }}</strong>,
    </p>

    <p style="margin:0 0 16px 0;">
        Enrollment Anda untuk topik pembelajaran berikut sudah berhasil diproses.
    </p>

    <p style="margin:0 0 6px 0;"><strong>Topik pembelajaran:</strong> {{ $enrollment->course->title }}</p>
    <p style="margin:0 0 6px 0;"><strong>Tanggal:</strong> {{ optional($enrollment->enrolled_at)->format('d M Y H:i') }}</p>
    <p style="margin:0 0 16px 0;"><strong>Status:</strong> {{ ucfirst($enrollment->status) }}</p>

    <p style="margin:0 0 16px 0;">
        Anda sudah bisa mulai belajar dan melihat progres pembelajaran dari halaman topik pembelajaran.
    </p>

    @component('emails.components.button', ['url' => url('/courses/' . $enrollment->course->slug), 'color' => '#0f766e'])
        Buka topik pembelajaran
    @endcomponent
@endcomponent
