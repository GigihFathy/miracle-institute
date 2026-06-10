@props([
    'title' => config('app.name'),
    'accent' => '#004777',
    'accentSoft' => '#eef8ff',
    'badge' => null,
    'heroTitle' => null,
    'heroText' => null,
    'heroImage' => null,
    'heroImageAlt' => 'Ilustrasi email',
])

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f8fc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f8fc;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:720px;background:#ffffff;border:1px solid #d7e7f7;border-radius:28px;overflow:hidden;box-shadow:0 18px 44px rgba(0,71,119,.10);">
                    <tr>
                        <td style="padding:30px 32px;background:linear-gradient(135deg, {{ $accent }} 0%, #035a93 60%, #35A7FF 100%);color:#fff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <div style="font-size:12px;letter-spacing:.18em;text-transform:uppercase;opacity:.84;font-weight:700;">
                                            {{ config('app.name') }}
                                        </div>
                                        <div style="font-size:28px;font-weight:800;margin-top:10px;line-height:1.25;">
                                            {{ $title }}
                                        </div>
                                        @if($badge)
                                            <div style="margin-top:14px;">
                                                <span style="display:inline-block;background:rgba(255,255,255,.16);color:#fff;border:1px solid rgba(255,255,255,.20);padding:7px 12px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.04em;">
                                                    {{ $badge }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if($heroTitle || $heroText)
                    <tr>
                        <td style="padding:28px 32px 0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $accentSoft }};border:1px solid #d7e7f7;border-radius:22px;overflow:hidden;">
                                <tr>
                                    <td style="padding:24px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="vertical-align:middle;{{ $heroImage ? 'padding-right:22px;' : '' }}">
                                                    <div style="font-size:20px;line-height:1.35;font-weight:800;color:#004777;">
                                                        {{ $heroTitle }}
                                                    </div>
                                                    @if($heroText)
                                                        <div style="margin-top:8px;font-size:14px;line-height:1.7;color:#334155;">
                                                            {{ $heroText }}
                                                        </div>
                                                    @endif
                                                </td>
                                                @if($heroImage)
                                                    <td align="right" width="210" style="vertical-align:middle;">
                                                        <img src="{{ $heroImage }}" alt="{{ $heroImageAlt }}" style="display:block;width:100%;max-width:210px;height:auto;border:0;">
                                                    </td>
                                                @endif
                                            </tr>
                                        </table>
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
                            {{ $footer ?? view('emails.components.footer') }}
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 32px;border-top:1px solid #d7e7f7;font-size:12px;color:#64748b;line-height:1.7;background:#f8fbff;">
                            Email ini dikirim otomatis oleh sistem pembelajaran. Mohon jangan membalas email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
