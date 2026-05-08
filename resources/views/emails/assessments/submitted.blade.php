@component('emails.layouts.learning', [
    'title' => 'Submission Diterima',
    'accent' => '#334155',
    'accentSoft' => '#f1f5f9',
    'badge' => 'RECEIPT',
    'icon' => '📨',
    'heroTitle' => 'Jawaban Anda telah diterima',
    'heroText' => 'Sistem mencatat submission assessment sebagai bukti penerimaan jawaban.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, berikut detail submission Anda:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Assessment', 'value' => $attempt->assessment->title])
        @include('emails.partials.meta-row', ['label' => 'Attempt', 'value' => 'Attempt #' . $attempt->attempt_no])
        @include('emails.partials.meta-row', ['label' => 'Submitted At', 'value' => optional($attempt->submitted_at)->format('d M Y H:i')])
    </table>
@endcomponent