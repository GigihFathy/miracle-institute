@props([
    'title',
    'description' => null,
    'accent' => '#0f172a',
    'soft' => '#e2e8f0',
    'icon' => '✨',
])

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $soft }};border-radius:20px;overflow:hidden;">
    <tr>
        <td style="padding:22px 24px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="vertical-align:middle;">
                        <div style="font-size:22px;line-height:1.35;font-weight:800;color:#0f172a;">
                            {{ $title }}
                        </div>
                        @if($description)
                            <div style="margin-top:8px;font-size:14px;line-height:1.7;color:#475569;">
                                {{ $description }}
                            </div>
                        @endif
                    </td>
                    <td align="right" style="vertical-align:middle;">
                        <div style="width:58px;height:58px;border-radius:18px;background:{{ $accent }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:26px;">
                            {{ $icon }}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>