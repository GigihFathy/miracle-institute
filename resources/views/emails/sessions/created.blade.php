@component('emails.layouts.learning', [
    'title' => 'Sesi Video Baru',
    'accent' => '#004777',
    'accentSoft' => '#eef8ff',
    'badge' => 'SESI BARU',
    'icon' => 'SE',
    'heroTitle' => 'Sesi pembelajaran baru sudah dijadwalkan',
    'heroText' => 'Silakan cek detail sesi dan siapkan kehadiran Anda agar tidak tertinggal materi.',
    'heroImage' => asset('images/decor/session.png'),
    'heroImageAlt' => 'Ilustrasi sesi pembelajaran',
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, ada sesi pembelajaran baru yang baru saja dibuat untuk kursus Anda.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #d7e7f7;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Kursus', 'value' => $session->topic->course->title])
        @include('emails.partials.meta-row', ['label' => 'Topik', 'value' => $session->topic->name])
        @include('emails.partials.meta-row', ['label' => 'Judul Sesi', 'value' => $session->title])
        @include('emails.partials.meta-row', ['label' => 'Mulai', 'value' => optional($session->start_at)->format('d M Y H:i')])
        @include('emails.partials.meta-row', ['label' => 'Selesai', 'value' => optional($session->end_at)->format('d M Y H:i')])
    </table>

    @component('emails.components.button', ['url' => url('/topics/' . $session->topic->slug), 'color' => '#004777'])
        Buka Materi
    @endcomponent
@endcomponent
