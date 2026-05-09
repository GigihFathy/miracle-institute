@component('emails.layouts.learning', [
    'title' => 'Reminder Sesi',
    'accent' => '#0284c7',
    'accentSoft' => '#eff6ff',
    'badge' => 'REMINDER',
    'icon' => '⏰',
    'heroTitle' => 'Sesi akan segera dimulai',
    'heroText' => 'Masuk tepat waktu untuk menjaga absensi dan memastikan Anda tidak tertinggal materi.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, sesi berikut akan dimulai sebentar lagi.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Sesi', 'value' => $session->title])
        @include('emails.partials.meta-row', ['label' => 'Mulai', 'value' => optional($session->start_at)->format('d M Y H:i')])
        @include('emails.partials.meta-row', ['label' => 'Link', 'value' => 'Silakan buka halaman course'])
    </table>

    @component('emails.components.button', ['url' => url('/sessions/' . $session->id), 'color' => '#0284c7'])
        Masuk ke Sesi
    @endcomponent
@endcomponent