<x-emails.layouts.learning title="Assessment Baru Tersedia">
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Halo,</p>
    <p style="margin:0 0 12px;font-size:15px;line-height:1.8;color:#334155;">Assessment <strong>{{ $assessment->title }}</strong> kini tersedia untuk course <strong>{{ $course->title }}</strong>.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:18px;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
        <tr><td style="padding:18px;background:#f8fafc;font-size:13px;font-weight:700;color:#0f172a;">Detail Assessment</td></tr>
        <tr><td style="padding:18px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr><td style="padding:8px 0;color:#64748b;">Passing Grade</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $assessment->passing_grade }}</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Timer</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $assessment->time_limit_minutes ?? '-' }} menit</td></tr>
                <tr><td style="padding:8px 0;color:#64748b;">Questions</td><td style="padding:8px 0;text-align:right;font-weight:700;">{{ $assessment->questions()->count() }}</td></tr>
            </table>
        </td></tr>
    </table>

    <x-emails.components.button :url="$url">Buka Course</x-emails.components.button>
</x-emails.layouts.learning>