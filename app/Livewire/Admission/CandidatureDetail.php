<?php

namespace App\Livewire\Admission;

use App\Models\Candidature;
use App\Models\DocumentCandidature;
use App\Models\User;
use App\Services\VerificationDocumentCandidature;
use App\Services\WorkflowCandidature;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class CandidatureDetail extends Component
{
    #[Locked]
    public int $candidatureId;

    #[Locked]
    public ?int $documentARejeter = null;

    public string $motifRejet = '';

    public function mount(Candidature $candidature): void
    {
        Gate::authorize('view', $candidature);
        $this->candidatureId = $candidature->id;
    }

    public function prendreEnCharge(WorkflowCandidature $workflow): void
    {
        $workflow->prendreEnCharge($this->dossier(), $this->utilisateur());
        session()->flash('status', 'La candidature est maintenant en cours d’étude.');
    }

    public function reprendreTraitement(WorkflowCandidature $workflow): void
    {
        $workflow->reprendreTraitement($this->dossier(), $this->utilisateur());
        session()->flash('status', 'Le traitement de la candidature a repris.');
    }

    public function transmettreAuJury(WorkflowCandidature $workflow): void
    {
        $workflow->transmettreAuJury($this->dossier(), $this->utilisateur());
        session()->flash('status', 'La candidature a été transmise au jury.');
    }

    public function validerDocument(int $documentId, VerificationDocumentCandidature $verification): void
    {
        $verification->valider($this->documentDuDossier($documentId), $this->utilisateur());
        $this->fermerRejet();
        session()->flash('status', 'Le document a été validé.');
    }

    public function preparerRejet(int $documentId): void
    {
        $document = $this->documentDuDossier($documentId);
        Gate::forUser($this->utilisateur())->authorize('verifier', $document);

        $this->documentARejeter = $document->id;
        $this->motifRejet = '';
        $this->resetValidation('motifRejet');
    }

    public function rejeterDocument(VerificationDocumentCandidature $verification): void
    {
        $donnees = $this->validate([
            'documentARejeter' => ['required', 'integer'],
            'motifRejet' => ['required', 'string', 'max:1000'],
        ], [
            'motifRejet.required' => 'Le motif du rejet est obligatoire.',
            'motifRejet.max' => 'Le motif ne peut pas dépasser 1000 caractères.',
        ]);

        $verification->rejeter(
            $this->documentDuDossier((int) $donnees['documentARejeter']),
            $this->utilisateur(),
            $donnees['motifRejet'],
        );

        $this->fermerRejet();
        session()->flash('status', 'Le document a été rejeté et le motif a été enregistré.');
    }

    public function fermerRejet(): void
    {
        $this->documentARejeter = null;
        $this->motifRejet = '';
        $this->resetValidation('motifRejet');
    }

    public function render()
    {
        $candidature = $this->dossier()->load([
            'candidat',
            'programme.typesDocuments',
            'documents.typeDocument',
            'documents.verificateur',
            'historiques.utilisateur',
            'messages.utilisateur',
            'agentTransmission',
            'auteurDecision',
        ]);

        return view('livewire.admission.candidature-detail', [
            'candidature' => $candidature,
            'documentsManquants' => $candidature->documentsObligatoiresNonValides(),
        ])->title('Dossier '.$candidature->code_suivi.' - SGA EPF');
    }

    private function dossier(): Candidature
    {
        $candidature = Candidature::query()->findOrFail($this->candidatureId);
        Gate::forUser($this->utilisateur())->authorize('view', $candidature);

        return $candidature;
    }

    private function documentDuDossier(int $documentId): DocumentCandidature
    {
        return $this->dossier()->documents()->findOrFail($documentId);
    }

    private function utilisateur(): User
    {
        /** @var User $utilisateur */
        $utilisateur = auth()->user();

        return $utilisateur;
    }
}
