@component('emails.layouts.learning', [
    'title' => 'Topik Selesai',
    'accent' => '#4f46e5',
    'accentSoft' => '#eef2ff',
    'badge' => 'PROGRESS',
    'icon' => '📘',
    'heroTitle' => 'Satu topik berhasil diselesaikan',
    'heroText' => 'Progres Anda bertambah. Selesaikan topik berikutnya untuk membuka assessment dan sertifikat.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, topik berikut sudah selesai:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Topik', 'value' => $progress->topic->name])
        @include('emails.partials.meta-row', ['label' => 'Course', 'value' => $progress->courseEnrollment->course->title])
        @include('emails.partials.meta-row', ['label' => 'Status', 'value' => ucfirst($progress->status)])
    </table>

    @component('emails.components.button', ['url' => url('/topics/' . $progress->topic->slug), 'color' => '#4f46e5'])
        Lanjutkan Topik
    @endcomponent
@endcomponent