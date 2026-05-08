@props([
    'url',
    'color' => '#0f172a',
])

<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 12px 0;">
    <tr>
        <td>
            <a href="{{ $url }}"
               style="display:inline-block;background:{{ $color }};color:#ffffff;text-decoration:none;padding:13px 20px;border-radius:14px;font-size:14px;font-weight:800;letter-spacing:.01em;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>