@props(['url'])
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td>
            <a href="{{ $url }}" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:12px;font-size:14px;font-weight:700;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>