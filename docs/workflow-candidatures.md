# Workflow des candidatures

Le champ `candidatures.statut` ne doit jamais etre modifie directement depuis un controleur ou un composant Livewire du back-office.

Toutes les actions passent par `App\Services\WorkflowCandidature`. Ce service verifie le role, le statut courant, la completude du dossier et enregistre l'historique dans une transaction.

## Transitions

```text
brouillon -> soumise
soumise -> en_traitement_admission
en_traitement_admission -> complement_demande
complement_demande -> en_traitement_admission
en_traitement_admission -> transmise_au_jury
transmise_au_jury -> complement_demande
transmise_au_jury -> admise | refusee
```

Les statuts `admise`, `refusee` et `abandonnee` sont finaux.

## Responsabilites

- Service admission : prendre en charge, demander un complement, reprendre le traitement et transmettre au jury.
- Jury : demander un complement sur un dossier transmis, admettre ou refuser.
- Super administrateur : peut executer les actions internes, mais reste soumis aux memes transitions.
- Candidat : soumet sa candidature par le service public dedie.

## Utilisation

```php
use App\Enums\StatutCandidature;
use App\Services\WorkflowCandidature;

$workflow = app(WorkflowCandidature::class);

$workflow->prendreEnCharge($candidature, auth()->user());
$workflow->demanderComplement($candidature, auth()->user(), $message);
$workflow->reprendreTraitement($candidature, auth()->user());
$workflow->transmettreAuJury($candidature, auth()->user());
$workflow->decider($candidature, auth()->user(), StatutCandidature::Admise, $commentaire);
```

La transmission au jury est refusee tant que chaque document obligatoire du programme ne possede pas un document au statut `valide`. Un refus du jury exige un motif.
