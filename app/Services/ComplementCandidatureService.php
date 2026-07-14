<?php

namespace App\Services;

use App\Enums\StatutCandidature;
use App\Models\Candidature;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ComplementCandidatureService
{
    /**
     * @param  array<int|string, UploadedFile>  $fichiers
     */
    public function soumettre(Candidature $candidature, array $fichiers, ?string $message): Candidature
    {
        $nouveauxChemins = [];
        $anciensChemins = [];

        try {
            $resultat = DB::transaction(function () use (
                $candidature,
                $fichiers,
                $message,
                &$nouveauxChemins,
                &$anciensChemins,
            ): Candidature {
                $dossier = Candidature::query()
                    ->with('programme.typesDocuments')
                    ->lockForUpdate()
                    ->findOrFail($candidature->id);

                if ($dossier->statut !== StatutCandidature::ComplementDemande) {
                    throw ValidationException::withMessages([
                        'code_suivi' => 'Ce dossier n’attend plus de complément.',
                    ]);
                }

                $typesAutorises = $dossier->programme->typesDocuments->modelKeys();

                foreach ($fichiers as $typeDocumentId => $fichier) {
                    $typeDocumentId = (int) $typeDocumentId;

                    if (! in_array($typeDocumentId, $typesAutorises, true)) {
                        throw ValidationException::withMessages([
                            'documents' => 'Un type de document transmis ne correspond pas au programme.',
                        ]);
                    }

                    $chemin = $fichier->store("candidatures/{$dossier->id}", 'local');
                    $nouveauxChemins[] = $chemin;

                    $documentExistant = $dossier->documents()
                        ->where('type_document_id', $typeDocumentId)
                        ->first();

                    if ($documentExistant && $documentExistant->chemin_fichier !== $chemin) {
                        $anciensChemins[] = $documentExistant->chemin_fichier;
                    }

                    $dossier->documents()->updateOrCreate(
                        ['type_document_id' => $typeDocumentId],
                        [
                            'nom_original' => $fichier->getClientOriginalName(),
                            'chemin_fichier' => $chemin,
                            'type_mime' => $fichier->getMimeType() ?? 'application/octet-stream',
                            'taille_octets' => Storage::disk('local')->size($chemin),
                            'statut' => 'en_attente',
                            'motif_rejet' => null,
                            'verifie_par' => null,
                            'verifie_le' => null,
                        ],
                    );
                }

                $contenu = trim((string) $message);
                $dossier->messages()->create([
                    'type' => 'message_candidat',
                    'visibilite' => 'candidat',
                    'contenu' => $contenu !== '' ? $contenu : 'Documents complémentaires transmis.',
                ]);

                $dossier->statut = StatutCandidature::EnTraitementAdmission;
                $dossier->save();
                $dossier->historiques()->create([
                    'ancien_statut' => StatutCandidature::ComplementDemande->value,
                    'nouveau_statut' => StatutCandidature::EnTraitementAdmission->value,
                    'acteur' => 'candidat',
                    'commentaire' => 'Complément transmis par le candidat.',
                ]);

                User::query()
                    ->where('actif', true)
                    ->whereHas('roles', fn ($query) => $query->whereIn('nom', ['super_admin', 'service_admission']))
                    ->each(function (User $utilisateur) use ($dossier): void {
                        $utilisateur->notificationsInternes()->create([
                            'type' => 'complement_candidat_recu',
                            'message' => 'Un candidat a transmis un complément de dossier.',
                            'donnees' => [
                                'candidature_id' => $dossier->id,
                                'code_suivi' => $dossier->code_suivi,
                            ],
                        ]);
                    });

                return $dossier->fresh(['candidat', 'programme', 'documents', 'messages', 'historiques']);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($nouveauxChemins);

            throw $exception;
        }

        Storage::disk('local')->delete(array_unique($anciensChemins));

        return $resultat;
    }
}
