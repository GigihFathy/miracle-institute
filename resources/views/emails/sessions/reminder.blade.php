@component('emails.layouts.learning', [
    'title' => 'Reminder Sesi',
    'accent' => '#004777',
    'accentSoft' => '#eef8ff',
    'badge' => 'PENGINGAT SESI',
    'icon' => 'JR',
    'heroTitle' => 'Sesi akan segera dimulai',
    'heroText' => 'Masuk tepat waktu agar kehadiran tercatat dengan baik dan Anda tidak tertinggal pembahasan.',
    'heroImage' => asset('images/decor/session.png'),
    'heroImageAlt' => 'Ilustrasi pengingat sesi',
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, sesi berikut akan dimulai sebentar lagi.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #d7e7f7;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Kursus', 'value' => $session->topic->course->title])
        @include('emails.partials.meta-row', ['label' => 'Topik', 'value' => $session->topic->name])
        @include('emails.partials.meta-row', ['label' => 'Sesi', 'value' => $session->title])
        @include('emails.partials.meta-row', ['label' => 'Mulai', 'value' => optional($session->start_at)->format('d M Y H:i')])
        @include('emails.partials.meta-row', ['label' => 'Akses', 'value' => 'Buka halaman sesi untuk bergabung'])
    </table>

    @component('emails.components.button', ['url' => url('/sessions/' . $session->id), 'color' => '#004777'])
        Masuk ke Sesi
    @endcomponent
@endcomponent
