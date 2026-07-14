<?php

namespace App\Livewire\Admission;

use App\Enums\StatutCandidature;
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

    public ?string $actionOuverte = null;

    public string $messageComplement = '';

    public string $decision = '';

    public string $commentaireDecision = '';

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
        $candidature = $workflow->transmettreAuJury($this->dossier(), $this->utilisateur());
        $this->alerterSiEmailEchoue($candidature, 'candidature_transmise_jury');
        session()->flash('status', 'La candidature a été transmise au jury.');
    }

    public function ouvrirDemandeComplement(): void
    {
        $candidature = $this->dossier();
        Gate::forUser($this->utilisateur())->authorize('demanderComplement', $candidature);

        $this->actionOuverte = 'complement';
        $this->messageComplement = '';
        $this->resetValidation();
    }

    public function demanderComplement(WorkflowCandidature $workflow): void
    {
        $donnees = $this->validate([
            'messageComplement' => ['required', 'string', 'max:2000'],
        ], [
            'messageComplement.required' => 'Le motif de la demande de complément est obligatoire.',
            'messageComplement.max' => 'Le message ne peut pas dépasser 2000 caractères.',
        ]);

        $candidature = $workflow->demanderComplement(
            $this->dossier(),
            $this->utilisateur(),
            $donnees['messageComplement'],
        );

        $this->fermerAction();
        $this->alerterSiEmailEchoue($candidature, 'complement_demande');
        session()->flash('status', 'La demande de complément a été envoyée au candidat.');
    }

    public function ouvrirDecision(string $decision): void
    {
        if (! in_array($decision, ['admise', 'refusee'], true)) {
            abort(422);
        }

        $candidature = $this->dossier();
        Gate::forUser($this->utilisateur())->authorize('decider', $candidature);

        $this->actionOuverte = 'decision';
        $this->decision = $decision;
        $this->commentaireDecision = '';
        $this->resetValidation();
    }

    public function enregistrerDecision(WorkflowCandidature $workflow): void
    {
        $donnees = $this->validate([
            'decision' => ['required', 'in:admise,refusee'],
            'commentaireDecision' => [
                $this->decision === 'refusee' ? 'required' : 'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'commentaireDecision.required' => 'Le motif du refus est obligatoire.',
            'commentaireDecision.max' => 'Le commentaire ne peut pas dépasser 2000 caractères.',
        ]);

        $decision = StatutCandidature::from($donnees['decision']);
        $candidature = $workflow->decider(
            $this->dossier(),
            $this->utilisateur(),
            $decision,
            trim($donnees['commentaireDecision']) ?: null,
        );

        $this->fermerAction();
        $evenement = $decision === StatutCandidature::Admise ? 'candidature_admise' : 'candidature_refusee';
        $this->alerterSiEmailEchoue($candidature, $evenement);
        session()->flash('status', $decision === StatutCandidature::Admise
            ? 'La candidature a été admise et le candidat a été informé.'
            : 'La candidature a été refusée et le candidat a été informé.');
    }

    public function fermerAction(): void
    {
        $this->actionOuverte = null;
        $this->messageComplement = '';
        $this->decision = '';
        $this->commentaireDecision = '';
        $this->resetValidation();
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
            'avisJury.jury',
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

    private function alerterSiEmailEchoue(Candidature $candidature, string $evenement): void
    {
        $email = $candidature->emailsEnvoyes()
            ->where('evenement', $evenement)
            ->latest('id')
            ->first();

        if ($email?->statut === 'echec') {
            session()->flash('warning', 'L’action est enregistrée, mais l’email n’a pas pu être envoyé. Consultez le journal des emails.');
        }
    }
}
