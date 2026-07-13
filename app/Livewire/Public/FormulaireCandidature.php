<?php

namespace App\Livewire\Public;

use App\Models\Candidature;
use App\Models\Programme;
use App\Rules\AgeMinimum;
use App\Services\CandidatureSubmissionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('layouts.public')]
class FormulaireCandidature extends Component
{
    use WithFileUploads;

    public Programme $programme;

    public int $etape = 1;

    public string $nom = '';

    public string $prenom = '';

    public ?string $date_naissance = null;

    public string $email = '';

    public string $telephone = '';

    public string $pays = 'Sénégal';

    public string $adresse = '';

    public string $derniere_formation = '';

    public string $etablissement_origine = '';

    /** @var array<int, mixed> */
    public array $documents = [];

    /** @var array<int, array{chemin: string, nom_original: string, type_mime: string, taille_octets: int}> */
    public array $documentsPersistes = [];

    public ?int $candidatureId = null;

    public ?string $confirmationCode = null;

    public function mount(Programme $programme): void
    {
        abort_unless($programme->actif && $programme->estOuvertAuxCandidatures(), 404);

        $this->programme = $programme->load(['typesDocuments']);

        if ($brouillonId = session('candidature_brouillon_'.$programme->id)) {
            $candidature = Candidature::query()
                ->with(['candidat', 'documents'])
                ->where('id', $brouillonId)
                ->where('programme_id', $programme->id)
                ->where('statut', 'brouillon')
                ->first();

            if ($candidature) {
                $this->chargerBrouillon($candidature);
            }
        }
    }

    public function etapeSuivante(): void
    {
        $this->validerEtapeCourante();
        $this->etape = min(3, $this->etape + 1);
    }

    public function etapePrecedente(): void
    {
        $this->etape = max(1, $this->etape - 1);
    }

    public function sauvegarderBrouillon()
    {
        try {
            if ($this->etape === 1) {
                $this->validerEtapeCourante();
            }

            $candidature = app(CandidatureSubmissionService::class)->sauvegarderBrouillon(
                $this->programme,
                $this->donneesFormulaire(),
                $this->candidatureId,
            );

            $this->candidatureId = $candidature->id;
            session()->put('candidature_brouillon_'.$this->programme->id, $candidature->id);

            session()->flash('message', 'Votre brouillon a été sauvegardé. Vous allez recevoir un email de confirmation avec votre code de suivi.');
            return $this->redirect(route('candidature.confirmation', $candidature->code_suivi), navigate: true);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('workflow', 'Une erreur technique est survenue lors de la sauvegarde du brouillon. Vérifiez les champs puis réessayez.');
        }
    }

    public function updatedDocuments(mixed $value, int|string $key): void
    {
        if ($value instanceof TemporaryUploadedFile) {
            $this->persisterDocumentUploade((int) $key, $value);
        }
    }

    public function soumettre()
    {
        try {
            $this->persisterDocumentsEnAttente();

for ($etape = 1; $etape <= 3; $etape++) {
                $this->etape = $etape;
                $this->validerEtapeCourante();
            }

            $candidature = app(CandidatureSubmissionService::class)->soumettre(
                $this->programme,
                $this->donneesFormulaire(),
                $this->documentsFichiersPourSoumission(),
                $this->candidatureId,
            );

            session()->forget('candidature_brouillon_'.$this->programme->id);
            $this->confirmationCode = $candidature->code_suivi;
            session()->flash('message', 'Votre candidature a bien été soumise. Votre code de suivi est : '.$candidature->code_suivi.'. Un e-mail sera envoyé si possible.');

            $this->dispatch('candidature-soumise', code: $candidature->code_suivi);

            return $this->redirect(route('candidature.confirmation', $candidature->code_suivi), navigate: true);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            \Log::error('Soumission candidature échouée', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            $this->addError('workflow', 'Une erreur technique est survenue lors de la soumission. Détails : '.$exception->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.public.formulaire-candidature', [
            'typesDocuments' => $this->programme->typesDocuments,
            'progression' => (int) round($this->etape / 3 * 100),
        ])->title('Candidature - '.$this->programme->nom);
    }

    private function chargerBrouillon(Candidature $candidature): void
    {
        $this->candidatureId = $candidature->id;
        $candidat = $candidature->candidat;

        $this->nom = $candidat->nom;
        $this->prenom = $candidat->prenom;
        $this->date_naissance = $candidat->date_naissance?->format('Y-m-d');
        $this->email = $candidat->email;
        $this->telephone = $candidat->telephone ?? '';
        $this->pays = $candidat->pays ?? '';
        $this->adresse = $candidat->adresse ?? '';
        $this->derniere_formation = $candidature->derniere_formation ?? '';
        $this->etablissement_origine = $candidature->etablissement_origine ?? '';

        foreach ($candidature->documents as $document) {
            if ($document->type_document_id) {
                $this->documentsPersistes[$document->type_document_id] = [
                    'chemin' => $document->chemin_fichier,
                    'nom_original' => $document->nom_original,
                    'type_mime' => $document->type_mime,
                    'taille_octets' => $document->taille_octets,
                ];
            }
        }
    }

    private function validerEtapeCourante(): void
    {
        $regles = match ($this->etape) {
            1 => $this->reglesEtape1(),
            2 => $this->reglesEtape2(),
            3 => $this->reglesEtape3(),
            default => [],
        };

        $this->resetErrorBag();

        $validator = validator(
            $this->only(array_keys($regles)),
            $regles
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if ($this->etape === 3) {
            $this->validerDocumentsObligatoires();
        }
    }

    private function validerDocumentsObligatoires(): void
    {
        $erreurs = [];

        foreach ($this->programme->typesDocuments as $type) {
            if (! $type->pivot->obligatoire) {
                continue;
            }

            $aFichierPersiste = isset($this->documentsPersistes[$type->id]);
            $aUploadEnCours = ! empty($this->documents[$type->id]);

            if (! $aFichierPersiste && ! $aUploadEnCours) {
                $erreurs["documents.{$type->id}"] = "Le document « {$type->nom} » est obligatoire.";
            }
        }

        if ($erreurs !== []) {
            throw ValidationException::withMessages($erreurs);
        }
    }

    private function persisterDocumentsEnAttente(): void
    {
        foreach ($this->documents as $typeId => $upload) {
            if ($upload instanceof TemporaryUploadedFile && ! isset($this->documentsPersistes[$typeId])) {
                $this->persisterDocumentUploade((int) $typeId, $upload);
            }
        }
    }

    private function persisterDocumentUploade(int $typeDocumentId, TemporaryUploadedFile $fichier): void
    {
        try {
            $dossier = $this->candidatureId
                ? "candidatures/brouillon/{$this->candidatureId}"
                : 'candidatures/brouillon/tmp/'.session()->getId();

            $ancienChemin = $this->documentsPersistes[$typeDocumentId]['chemin'] ?? null;
            if ($ancienChemin && Storage::disk('local')->exists($ancienChemin)) {
                Storage::disk('local')->delete($ancienChemin);
            }

            $chemin = $fichier->store($dossier, 'local');

            $this->documentsPersistes[$typeDocumentId] = [
                'chemin' => $chemin,
                'nom_original' => $fichier->getClientOriginalName(),
                'type_mime' => $fichier->getMimeType() ?? 'application/octet-stream',
                'taille_octets' => Storage::disk('local')->size($chemin),
            ];

            unset($this->documents[$typeDocumentId]);
        } catch (\Throwable $exception) {
            report($exception);
            unset($this->documents[$typeDocumentId]);
            $this->addError("documents.{$typeDocumentId}", 'Impossible de traiter ce fichier. Veuillez le téléverser à nouveau.');
        }
    }

    /**
     * @return array<int, TemporaryUploadedFile|array{chemin: string, nom_original: string, type_mime: string, taille_octets: int}>
     */
    private function documentsFichiersPourSoumission(): array
    {
        $fichiers = $this->documentsPersistes;

        foreach ($this->documents as $typeId => $upload) {
            if ($upload && ! isset($fichiers[$typeId])) {
                $fichiers[$typeId] = $upload;
            }
        }

        return $fichiers;
    }

    /**
     * @return array<string, mixed>
     */
    private function reglesEtape1(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['required', 'date', new AgeMinimum(17)],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('candidats', 'email')->ignore(
                    $this->candidatureId
                        ? Candidature::find($this->candidatureId)?->candidat_id
                        : null
                ),
            ],
            'telephone' => ['nullable', 'string', 'max:30'],
            'pays' => ['required', 'string', 'max:100'],
            'adresse' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function reglesEtape2(): array
    {
        return [
            'derniere_formation' => ['required', 'string', 'max:255'],
            'etablissement_origine' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    private function reglesEtape3(): array
    {
        if ($this->programme->typesDocuments->isEmpty()) {
            return [];
        }

        $regles = [
            'documents' => ['array', 'max:10'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];

        foreach ($this->programme->typesDocuments as $type) {
            $regles["documents.{$type->id}"] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
        }

        return $regles;
    }

    /**
     * @return array<string, mixed>
     */
    private function donneesFormulaire(): array
    {
        return [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'date_naissance' => $this->date_naissance,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'pays' => $this->pays,
            'adresse' => $this->adresse,
            'derniere_formation' => $this->derniere_formation,
            'etablissement_origine' => $this->etablissement_origine,
        ];
    }
}
