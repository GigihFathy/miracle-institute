@props([
    'url',
    'color' => '#0f172a',
])

<table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px 0 8px 0;">
    <tr>
        <td>
            <a href="{{ $url }}"
               style="display:inline-block;background:{{ $color }};color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:12px;font-size:14px;font-weight:700;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
