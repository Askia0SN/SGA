<?php

namespace App\Services;

use App\Enums\StatutCandidature;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\DocumentCandidature;
use App\Models\HistoriqueCandidature;
use App\Models\Programme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CandidatureSubmissionService
{
    public function __construct(
        private CodeSuiviGenerator $codeSuiviGenerator,
        private EmailService $emailService,
    ) {}

    /**
     * @param  array<string, mixed>  $donnees
     */
    public function sauvegarderBrouillon(Programme $programme, array $donnees, ?int $candidatureId = null): Candidature
    {
        $nouveauBrouillon = $candidatureId === null;
        $candidature = DB::transaction(function () use ($programme, $donnees, $candidatureId) {
            $candidature = $this->trouverOuCreerCandidature($programme, $donnees, $candidatureId);

            $candidature->update([
                'code_suivi' => $candidature->code_suivi ?? $this->codeSuiviGenerator->generer(),
                'derniere_formation' => $donnees['derniere_formation'] ?? $candidature->derniere_formation,
                'etablissement_origine' => $donnees['etablissement_origine'] ?? $candidature->etablissement_origine,
                'lettre_motivation' => $donnees['lettre_motivation'] ?? $candidature->lettre_motivation,
                'statut' => StatutCandidature::Brouillon,
            ]);

            return $candidature->fresh(['candidat', 'programme']);
        });

        if ($nouveauBrouillon) {
            try {
                $this->emailService->envoyerCandidatureBrouillon($candidature);
            } catch (\Throwable $exception) {
                report($exception);
                Log::error('Echec envoi email brouillon', [
                    'candidature_id' => $candidature->id,
                    'email' => $candidature->candidat->email,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }

        return $candidature;
    }

    /**
     * @param  array<string, mixed>  $donnees
     * @param  array<int, UploadedFile|array{chemin: string, nom_original: string, type_mime: string, taille_octets: int}>  $fichiers
     */
    public function soumettre(Programme $programme, array $donnees, array $fichiers, ?int $candidatureId = null): Candidature
    {
        $candidature = DB::transaction(function () use ($programme, $donnees, $fichiers, $candidatureId) {
            $candidature = $this->trouverOuCreerCandidature($programme, $donnees, $candidatureId);

            $ancienStatut = $candidature->statut;

            $candidature->update([
                'code_suivi' => $candidature->code_suivi ?? $this->codeSuiviGenerator->generer(),
                'derniere_formation' => $donnees['derniere_formation'],
                'etablissement_origine' => $donnees['etablissement_origine'],
                'lettre_motivation' => $donnees['lettre_motivation'] ?? $candidature->lettre_motivation,
                'statut' => StatutCandidature::Soumise,
                'soumise_le' => now(),
            ]);

            $this->enregistrerDocuments($candidature, $fichiers);
            $this->enregistrerHistorique($candidature, $ancienStatut, StatutCandidature::Soumise, 'Candidature soumise par le candidat');

            return $candidature->fresh(['candidat', 'programme', 'documents']);
        });

        try {
            $this->emailService->envoyerCandidatureSoumise($candidature);
        } catch (\Throwable $exception) {
            report($exception);
            Log::error('Echec envoi email soumission', [
                'candidature_id' => $candidature->id,
                'email' => $candidature->candidat->email,
                'exception' => $exception->getMessage(),
            ]);
        }

        return $candidature;
    }

    /**
     * @param  array<string, mixed>  $donnees
     */
    private function trouverOuCreerCandidature(Programme $programme, array $donnees, ?int $candidatureId): Candidature
    {
        $email = Str::lower(trim($donnees['email']));

        if ($candidatureId) {
            $candidature = Candidature::query()
                ->with('candidat')
                ->where('id', $candidatureId)
                ->where('programme_id', $programme->id)
                ->where('statut', StatutCandidature::Brouillon->value)
                ->firstOrFail();

            if (Str::lower($candidature->candidat->email) !== $email) {
                throw ValidationException::withMessages([
                    'email' => 'L’adresse email d’un brouillon enregistré ne peut pas être modifiée.',
                ]);
            }

            $candidature->candidat->update($this->donneesCandidat($donnees));

            return $candidature;
        }

        $candidat = Candidat::query()->firstOrCreate(
            ['email' => $email],
            $this->donneesCandidat($donnees),
        );

        $existante = Candidature::query()
            ->where('candidat_id', $candidat->id)
            ->where('programme_id', $programme->id)
            ->first();

        if ($existante) {
            throw ValidationException::withMessages([
                'email' => 'Une candidature existe déjà pour cette adresse email et ce programme.',
            ]);
        }

        $candidature = new Candidature([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'statut' => StatutCandidature::Brouillon,
        ]);

        $candidature->code_suivi = $this->codeSuiviGenerator->generer();
        $candidature->save();

        return $candidature;
    }

    /**
     * @param  array<string, mixed>  $donnees
     * @return array<string, mixed>
     */
    private function donneesCandidat(array $donnees): array
    {
        return [
            'prenom' => $donnees['prenom'],
            'nom' => $donnees['nom'],
            'date_naissance' => $donnees['date_naissance'],
            'telephone' => $donnees['telephone'] ?? null,
            'pays' => $donnees['pays'] ?? null,
            'adresse' => $donnees['adresse'] ?? null,
        ];
    }

    /**
     * @param  array<int, UploadedFile|array{chemin: string, nom_original: string, type_mime: string, taille_octets: int}>  $fichiers
     */
    private function enregistrerDocuments(Candidature $candidature, array $fichiers): void
    {
        if (empty($fichiers)) {
            return;
        }

        $totalFichiers = 0;

        foreach ($fichiers as $typeDocumentId => $upload) {
            if ($totalFichiers >= 10) {
                break;
            }

            if (is_array($upload) && isset($upload['chemin'])) {
                $this->enregistrerDocumentPersiste($candidature, (int) $typeDocumentId, $upload);
                $totalFichiers++;

                continue;
            }

            if ($upload instanceof UploadedFile) {
                $this->enregistrerDocumentUploade($candidature, (int) $typeDocumentId, $upload);
                $totalFichiers++;
            }
        }
    }

    /**
     * @param  array{chemin: string, nom_original: string, type_mime: string, taille_octets: int}  $meta
     */
    private function enregistrerDocumentPersiste(Candidature $candidature, int $typeDocumentId, array $meta): void
    {
        if (! Storage::disk('local')->exists($meta['chemin'])) {
            Log::warning('Fichier persisté introuvable', [
                'candidature_id' => $candidature->id,
                'chemin' => $meta['chemin'],
            ]);

            return;
        }

        $destination = "candidatures/{$candidature->id}/".basename($meta['chemin']);

        if ($meta['chemin'] !== $destination) {
            Storage::disk('local')->makeDirectory("candidatures/{$candidature->id}");
            Storage::disk('local')->move($meta['chemin'], $destination);
        }

        DocumentCandidature::updateOrCreate(
            [
                'candidature_id' => $candidature->id,
                'type_document_id' => $typeDocumentId ?: null,
            ],
            [
                'nom_original' => $meta['nom_original'],
                'chemin_fichier' => $destination,
                'type_mime' => $meta['type_mime'],
                'taille_octets' => $meta['taille_octets'],
                'statut' => 'en_attente',
            ],
        );
    }

    private function enregistrerDocumentUploade(Candidature $candidature, int $typeDocumentId, UploadedFile $fichier): void
    {
        try {
            if (! $fichier->isValid()) {
                Log::warning('Fichier upload invalide ignoré', [
                    'candidature_id' => $candidature->id,
                    'nom' => $fichier->getClientOriginalName(),
                ]);

                return;
            }

            $chemin = $fichier->store(
                "candidatures/{$candidature->id}",
                'local'
            );

            DocumentCandidature::updateOrCreate(
                [
                    'candidature_id' => $candidature->id,
                    'type_document_id' => $typeDocumentId ?: null,
                ],
                [
                    'nom_original' => $fichier->getClientOriginalName(),
                    'chemin_fichier' => $chemin,
                    'type_mime' => $fichier->getMimeType() ?? 'application/octet-stream',
                    'taille_octets' => Storage::disk('local')->size($chemin),
                    'statut' => 'en_attente',
                ],
            );
        } catch (\Throwable $exception) {
            Log::warning('Impossible d’enregistrer un document de candidature', [
                'candidature_id' => $candidature->id,
                'nom' => $fichier->getClientOriginalName(),
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function enregistrerHistorique(
        Candidature $candidature,
        ?StatutCandidature $ancienStatut,
        StatutCandidature $nouveauStatut,
        ?string $commentaire = null,
    ): void {
        HistoriqueCandidature::create([
            'candidature_id' => $candidature->id,
            'ancien_statut' => $ancienStatut?->value,
            'nouveau_statut' => $nouveauStatut->value,
            'acteur' => 'candidat',
            'commentaire' => $commentaire,
        ]);
    }
}
