<x-emails.layouts.learning title="Enrollment Berhasil">
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Halo {{ $user->full_name ?? $user->name }},</p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Pendaftaran Anda ke course <strong>{{ $course->title }}</strong> telah berhasil diproses.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <tr><td style="padding:18px;background:#f8fafc;font-size:13px;font-weight:700;color:#0f172a;">Ringkasan Enrollment</td></tr>
        <tr><td style="padding:18px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr><td style="padding:8px 0;color:#64748b;font-size:14px;">Course</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $course->title }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;font-size:14px;">Program</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $course->studyProgram?->title ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;font-size:14px;">Quota</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $course->quota }}</td></tr>
            </table>
        </td></tr>
    </table>

    <x-emails.components.button :url="$url">Buka Course</x-emails.components.button>

    <p style="margin:0;font-size:14px;line-height:1.8;color:#475569;">Silakan mulai dari topic pertama dan lanjutkan progres belajar Anda.</p>
</x-emails.layouts.learning>