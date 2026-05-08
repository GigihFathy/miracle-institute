<x-emails.layouts.learning title="Reminder Sesi">
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Halo {{ $session->topic?->course?->enrollments->first()?->user?->full_name ?? 'Student' }},</p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Sesi <strong>{{ $session->title }}</strong> akan dimulai pada <strong>{{ $session->start_at->format('d M Y, H:i') }}</strong>.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <tr><td style="padding:18px;background:#f8fafc;font-size:13px;font-weight:700;color:#0f172a;">Session Detail</td></tr>
        <tr><td style="padding:18px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr><td style="padding:8px 0;color:#64748b;">Course</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $course->title }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Topic</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $topic->name }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Platform</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $session->zoom_link ? 'Zoom' : 'Offline' }}</td></tr>
            </table>
        </td></tr>
    </table>

    <x-emails.components.button :url="$url">Buka Topic</x-emails.components.button>
</x-emails.layouts.learning>