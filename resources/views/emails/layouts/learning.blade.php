@props([
    'title' => config('app.name'),
    'accent' => '#0f172a',
    'accentSoft' => '#e2e8f0',
    'badge' => null,
    'icon' => '✨',
    'heroTitle' => null,
    'heroText' => null,
])

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:720px;background:#ffffff;border:1px solid #e2e8f0;border-radius:24px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,.06);">
                    <tr>
                        <td style="padding:28px 32px;background:linear-gradient(135deg, {{ $accent }} 0%, #111827 100%);color:#fff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <div style="font-size:12px;letter-spacing:.18em;text-transform:uppercase;opacity:.78;font-weight:700;">
                                            {{ config('app.name') }}
                                        </div>
                                        <div style="font-size:26px;font-weight:800;margin-top:10px;line-height:1.25;">
                                            {{ $title }}
                                        </div>
                                        @if($badge)
                                            <div style="margin-top:14px;">
                                                <span style="display:inline-block;background:rgba(255,255,255,.14);color:#fff;border:1px solid rgba(255,255,255,.18);padding:7px 12px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.04em;">
                                                    {{ $badge }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td align="right" style="vertical-align:middle;">
                                        <div style="width:64px;height:64px;border-radius:18px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.16);display:flex;align-items:center;justify-content:center;font-size:28px;">
                                            {{ $icon }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if($heroTitle || $heroText)
                    <tr>
                        <td style="padding:28px 32px 0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $accentSoft }};border-radius:20px;overflow:hidden;">
                                <tr>
                                    <td style="padding:24px 24px 22px 24px;">
                                        <div style="font-size:20px;line-height:1.35;font-weight:800;color:#0f172a;">
                                            {{ $heroTitle }}
                                        </div>
                                        @if($heroText)
                                            <div style="margin-top:8px;font-size:14px;line-height:1.7;color:#334155;">
                                                {{ $heroText }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td style="padding:32px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 28px 32px;">
                            {{ $footer ?? '' }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 32px;border-top:1px solid #e2e8f0;font-size:12px;color:#64748b;line-height:1.7;background:#f8fafc;">
                            Email ini dikirim otomatis oleh sistem pembelajaran. Mohon jangan membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>