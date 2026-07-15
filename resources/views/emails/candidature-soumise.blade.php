@php
    $presentation = match ($evenement) {
        'candidature_brouillon' => ['label' => 'Brouillon enregistré', 'accent' => '#6f22de'],
        'complement_demande' => ['label' => 'Action requise', 'accent' => '#d91426'],
        'candidature_transmise_jury' => ['label' => 'Dossier transmis au jury', 'accent' => '#6f22de'],
        'candidature_admise' => ['label' => 'Décision d’admission', 'accent' => '#198754'],
        'candidature_refusee' => ['label' => 'Décision du jury', 'accent' => '#d91426'],
        default => ['label' => 'Candidature reçue', 'accent' => '#d91426'],
    };
    $logoSource = isset($message)
        ? $message->embed(public_path('images/logo-sga.png'))
        : asset('images/logo-sga.png');
@endphp

<x-email-layout
    :title="$presentation['label']"
    :preheader="$objetPersonnalise"
    :accent="$presentation['accent']"
    :action-url="route('candidatures.suivi')"
    action-label="Suivre ma candidature"
    :logo-src="$logoSource"
    :signature="$signature"
    footer-note="Email automatique du système d’admission EPF Africa. Ne communiquez pas votre code de suivi à un tiers."
>
    <div style="color: #4f4965; font-size: 16px; line-height: 26px;">
        {!! $contenuHtml !!}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="width: 100%; margin: 24px 0; border: 1px solid #e7e1f0; border-radius: 6px; background-color: #faf9fc;">
        <tr>
            <td style="padding: 18px 20px; border-bottom: 1px solid #e7e1f0;">
                <p style="margin: 0 0 4px; color: #817a94; font-size: 11px; font-weight: 800; text-transform: uppercase;">Programme</p>
                <p style="margin: 0; color: #27185f; font-size: 15px; font-weight: 700;">{{ $candidature->programme->nom }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 18px 20px;">
                <p style="margin: 0 0 6px; color: #817a94; font-size: 11px; font-weight: 800; text-transform: uppercase;">Code de suivi</p>
                <p style="margin: 0; color: #d91426; font-family: Consolas, 'Courier New', monospace; font-size: 21px; font-weight: 800; letter-spacing: 1px;">{{ $candidature->code_suivi }}</p>
            </td>
        </tr>
    </table>

    <p style="margin: 0; color: #817a94; font-size: 13px; line-height: 20px;">
        Conservez ce code : il vous permettra de consulter l’avancement de votre dossier avec votre adresse email.
    </p>
</x-email-layout>
