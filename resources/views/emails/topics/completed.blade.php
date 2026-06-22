@component('emails.layouts.learning', [
    'title' => 'Sesi Selesai',
    'accent' => '#4f46e5',
    'accentSoft' => '#eef2ff',
    'badge' => 'PROGRESS',
    'icon' => '📘',
    'heroTitle' => 'Satu sesi berhasil diselesaikan',
    'heroText' => 'Progres Anda bertambah. Selesaikan sesi berikutnya untuk membuka assessment dan sertifikat.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, sesi berikut sudah selesai:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Sesi', 'value' => $progress->topic->name])
        @include('emails.partials.meta-row', ['label' => 'Topik pembelajaran', 'value' => $progress->courseEnrollment->course->title])
        @include('emails.partials.meta-row', ['label' => 'Status', 'value' => ucfirst($progress->status)])
    </table>

    @component('emails.components.button', ['url' => url('/topics/' . $progress->topic->slug), 'color' => '#4f46e5'])
        Lanjutkan Sesi
    @endcomponent
@endcomponent