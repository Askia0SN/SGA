@php
    $logoSource = isset($message)
        ? $message->embed(public_path('images/logo-sga.png'))
        : asset('images/logo-sga.png');
@endphp

<x-email-layout
    title="Bienvenue dans l’espace admission"
    preheader="Votre accès professionnel EPF est prêt à être activé."
    eyebrow="Accès professionnel"
    accent="#6f22de"
    :action-url="$url"
    action-label="Définir mon mot de passe"
    :logo-src="$logoSource"
>
    <p style="margin: 0 0 16px;">Bonjour <strong style="color: #27185f;">{{ $nom }}</strong>,</p>
    <p style="margin: 0 0 16px;">
        {{ $invitePar }} a créé votre accès au système d’admission EPF. Utilisez le bouton ci-dessous pour définir votre mot de passe et activer votre compte.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; margin: 24px 0; border-left: 4px solid #6f22de; background-color: #f7f4fc;">
        <tr>
            <td style="padding: 16px 18px; color: #4f4965; font-size: 14px; line-height: 22px;">
                Ce lien personnel expire dans <strong style="color: #27185f;">{{ $expiration }} minutes</strong>. Il ne doit pas être transféré.
            </td>
        </tr>
    </table>

    <p style="margin: 0; color: #817a94; font-size: 13px; line-height: 20px;">
        Si vous ne reconnaissez pas cette invitation, vous pouvez ignorer cet email en toute sécurité.
    </p>
</x-email-layout>
