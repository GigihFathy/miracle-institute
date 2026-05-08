<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background:#f8fafc; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background:#ffffff; border:1px solid #e2e8f0; border-radius:20px; overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px; background:#0f172a; color:#ffffff;">
                            <div style="font-size:12px; letter-spacing:.18em; text-transform:uppercase; opacity:.75;">
                                {{ config('app.name') }}
                            </div>
                            <div style="font-size:24px; font-weight:700; margin-top:10px;">
                                {{ $title ?? 'Notification' }}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px; border-top:1px solid #e2e8f0; font-size:12px; color:#64748b;">
                            Email ini dikirim otomatis oleh sistem. Jangan membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>