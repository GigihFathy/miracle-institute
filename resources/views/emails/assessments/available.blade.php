@component('emails.layouts.learning', [
    'title' => 'Assessment Tersedia',
    'accent' => '#b45309',
    'accentSoft' => '#fffbeb',
    'badge' => 'ASSESSMENT',
    'icon' => '📝',
    'heroTitle' => 'Assessment sudah dibuka',
    'heroText' => 'Seluruh sesi sudah selesai dan assessment kini tersedia untuk dikerjakan.'
])
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.8;color:#334155;">
        Halo <strong>{{ $notifiable->name }}</strong>, Anda sudah dapat mulai mengerjakan assessment berikut.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;margin-bottom:22px;">
        @include('emails.partials.meta-row', ['label' => 'Assessment', 'value' => $assessment->title])
        @include('emails.partials.meta-row', ['label' => 'Passing Grade', 'value' => $assessment->passing_grade . '%'])
    </table>

    @component('emails.components.button', ['url' => url('/assessments/' . $assessment->id), 'color' => '#b45309'])
        Kerjakan Assessment
    @endcomponent
@endcomponent