@props(['statut'])

@php
    $valeur = $statut instanceof \App\Enums\StatutCandidature ? $statut->value : (string) $statut;
    $libelle = $statut instanceof \App\Enums\StatutCandidature
        ? $statut->libelle()
        : (\App\Enums\StatutCandidature::tryFrom($valeur)?->libelle() ?? ucfirst(str_replace('_', ' ', $valeur)));

    $classes = match ($valeur) {
        'soumise' => 'border-[#d8d0ea] bg-[#f4f1fb] text-[#27185f]',
        'en_traitement_admission' => 'border-[#bddbea] bg-[#f0f8fc] text-[#185875]',
        'complement_demande' => 'border-[#f0d8a8] bg-[#fff9e9] text-[#805c12]',
        'transmise_au_jury' => 'border-[#cfc7e8] bg-[#eeeafd] text-[#4b3788]',
        'admise' => 'border-[#b9dfcc] bg-[#f1fbf6] text-[#17603a]',
        'refusee', 'abandonnee' => 'border-[#f0c8ce] bg-[#fff4f5] text-[#b70f1e]',
        default => 'border-[#dedbe7] bg-[#f7f6f9] text-[#625d70]',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold', $classes]) }}>
    {{ $libelle }}
</span>
