@props([
    'title',
    'preheader' => '',
    'eyebrow' => 'Admissions EPF Africa',
    'accent' => '#d91426',
    'actionUrl' => null,
    'actionLabel' => null,
    'logoSrc' => null,
    'signature' => 'Service Admission - EPF Africa',
    'footerNote' => 'Cet email a été envoyé automatiquement par le système d’admission EPF Africa.',
])

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $title }}</title>
    <style>
        @media only screen and (max-width: 640px) {
            .email-shell { width: 100% !important; }
            .email-padding { padding-left: 24px !important; padding-right: 24px !important; }
            .email-title { font-size: 26px !important; line-height: 32px !important; }
            .email-button { display: block !important; text-align: center !important; }
        }

        .email-content p { margin: 0 0 16px; }
        .email-content a { color: #d91426; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f3f8; color: #191339; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%;">
    <div style="display: none; max-height: 0; overflow: hidden; opacity: 0; color: transparent;">
        {{ $preheader }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; background-color: #f5f3f8;">
        <tr>
            <td align="center" style="padding: 32px 12px;">
                <table role="presentation" width="640" cellpadding="0" cellspacing="0" class="email-shell" style="width: 640px; max-width: 640px; overflow: hidden; background-color: #ffffff; border: 1px solid #e7e1f0; border-radius: 8px;">
                    <tr>
                        <td height="6" style="height: 6px; background-color: {{ $accent }}; font-size: 0; line-height: 0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="email-padding" style="padding: 28px 40px 22px; border-bottom: 1px solid #eeeaf4;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        @if ($logoSrc)
                                            <img src="{{ $logoSrc }}" width="74" alt="SGA EPF" style="display: block; width: 74px; height: auto; border: 0;">
                                        @else
                                            <strong style="font-size: 24px; color: #27185f;">EPF</strong>
                                        @endif
                                    </td>
                                    <td align="right" style="vertical-align: middle; color: #6d6684; font-size: 12px; font-weight: 700; letter-spacing: 0; text-transform: uppercase;">
                                        Système d'admission
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-padding" style="padding: 36px 40px 12px;">
                            <p style="margin: 0 0 10px; color: {{ $accent }}; font-size: 12px; font-weight: 800; letter-spacing: 0; text-transform: uppercase;">{{ $eyebrow }}</p>
                            <h1 class="email-title" style="margin: 0; color: #191339; font-size: 30px; font-weight: 800; line-height: 38px; letter-spacing: 0;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-padding email-content" style="padding: 20px 40px 12px; color: #4f4965; font-size: 16px; line-height: 26px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    @if ($actionUrl && $actionLabel)
                        <tr>
                            <td class="email-padding" style="padding: 12px 40px 36px;">
                                <a href="{{ $actionUrl }}" class="email-button" style="display: inline-block; padding: 14px 22px; border-radius: 6px; background-color: #d91426; color: #ffffff; font-size: 14px; font-weight: 800; line-height: 20px; text-decoration: none;">{{ $actionLabel }}</a>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="email-padding" style="padding: 24px 40px; background-color: #27185f; color: #ffffff;">
                            <p style="margin: 0 0 4px; font-size: 13px; font-weight: 800;">{{ $signature }}</p>
                            <p style="margin: 0; color: #ded8ee; font-size: 12px; line-height: 18px;">Gérer · Évaluer · Admettre</p>
                        </td>
                    </tr>
                </table>
                <p style="margin: 18px auto 0; color: #817a94; font-size: 11px; line-height: 17px; text-align: center;">
                    {{ $footerNote }}
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
