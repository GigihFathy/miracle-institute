@props([
    'title' => config('app.name'),
])

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;">
                    <tr>
                        <td style="padding:0 24px 12px 24px;">
                            <div style="font-size:12px;letter-spacing:.14em;text-transform:uppercase;font-weight:700;color:#6b7280;">
                                Miracle Institute
                            </div>
                            <div style="font-size:28px;font-weight:700;line-height:1.3;color:#004777;">
                                {{ $title }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 12px 24px;">
                            <div style="border-top:1px solid #e5e7eb;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 24px 24px 24px;font-size:15px;line-height:1.8;color:#374151;">
                            {{ $slot }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 24px 18px 24px;">
                            {{ $footer ?? view('emails.components.footer') }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 24px 0 24px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;line-height:1.7;">
                            Email ini dikirim otomatis oleh sistem pembelajaran. Mohon jangan membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
