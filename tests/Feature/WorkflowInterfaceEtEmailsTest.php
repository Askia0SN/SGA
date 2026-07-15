<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Livewire\Admission\CandidatureDetail;
use App\Livewire\Admission\CandidaturesListe;
use App\Mail\CandidatureSoumiseMail;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\DocumentCandidature;
use App\Models\Programme;
use App\Models\TypeDocument;
use App\Models\User;
use App\Services\WorkflowCandidature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class WorkflowInterfaceEtEmailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_admission_demande_un_complement_et_le_candidat_est_informe(): void
    {
        Mail::fake();
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);

        $this->actingAs($agent);

        Livewire::test(CandidatureDetail::class, ['candidature' => $candidature])
            ->call('ouvrirDemandeComplement')
            ->set('messageComplement', 'Merci de fournir le relevé complet.')
            ->call('demanderComplement')
            ->assertHasNoErrors()
            ->assertSee('La demande de complément a été envoyée');

        $this->assertSame(StatutCandidature::ComplementDemande, $candidature->fresh()->statut);
        $this->assertDatabaseHas('messages_candidature', [
            'candidature_id' => $candidature->id,
            'type' => 'demande_complement',
            'visibilite' => 'candidat',
            'contenu' => 'Merci de fournir le relevé complet.',
        ]);
        $this->assertDatabaseHas('emails_envoyes', [
            'candidature_id' => $candidature->id,
            'user_id' => $agent->id,
            'evenement' => 'complement_demande',
            'statut' => 'envoye',
        ]);
        Mail::assertSent(CandidatureSoumiseMail::class);

        $this->post(route('candidatures.suivi.rechercher'), [
            'code_suivi' => $candidature->code_suivi,
            'email' => $candidature->candidat->email,
        ])->assertSee('Merci de fournir le relevé complet.');
    }

    public function test_transmission_informe_le_candidat_et_les_membres_du_jury(): void
    {
        Mail::fake();
        $agent = $this->utilisateurAvecRole('service_admission');
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);
        $this->ajouterDocumentValide($candidature);

        $resultat = app(WorkflowCandidature::class)->transmettreAuJury($candidature, $agent);

        $this->assertSame(StatutCandidature::TransmiseAuJury, $resultat->statut);
        $this->assertDatabaseHas('emails_envoyes', [
            'candidature_id' => $candidature->id,
            'evenement' => 'candidature_transmise_jury',
            'statut' => 'envoye',
        ]);
        $this->assertDatabaseHas('notifications_internes', [
            'user_id' => $jury->id,
            'type' => 'candidature_transmise_jury',
        ]);
        Mail::assertSent(CandidatureSoumiseMail::class);
    }

    public function test_jury_admet_une_candidature_depuis_la_fiche(): void
    {
        Mail::fake();
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::TransmiseAuJury);
        $this->actingAs($jury);

        Livewire::test(CandidatureDetail::class, ['candidature' => $candidature])
            ->call('ouvrirDecision', 'admise')
            ->set('commentaireDecision', 'Avis favorable du jury.')
            ->call('enregistrerDecision')
            ->assertHasNoErrors()
            ->assertSee('La candidature a été admise');

        $this->assertSame(StatutCandidature::Admise, $candidature->fresh()->statut);
        $this->assertDatabaseHas('avis_jury', [
            'candidature_id' => $candidature->id,
            'jury_id' => $jury->id,
            'decision' => 'admettre',
        ]);
        $this->assertDatabaseHas('emails_envoyes', [
            'candidature_id' => $candidature->id,
            'evenement' => 'candidature_admise',
            'statut' => 'envoye',
        ]);
    }

    public function test_jury_doit_motiver_un_refus(): void
    {
        Mail::fake();
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::TransmiseAuJury);
        $this->actingAs($jury);

        Livewire::test(CandidatureDetail::class, ['candidature' => $candidature])
            ->call('ouvrirDecision', 'refusee')
            ->call('enregistrerDecision')
            ->assertHasErrors(['commentaireDecision' => 'required'])
            ->set('commentaireDecision', 'Prérequis académiques insuffisants.')
            ->call('enregistrerDecision')
            ->assertHasNoErrors();

        $this->assertSame(StatutCandidature::Refusee, $candidature->fresh()->statut);
        $this->assertDatabaseHas('emails_envoyes', [
            'candidature_id' => $candidature->id,
            'evenement' => 'candidature_refusee',
            'statut' => 'envoye',
        ]);
    }

    public function test_l_espace_jury_affiche_ses_indicateurs_specifiques(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $this->creerCandidature(StatutCandidature::TransmiseAuJury);
        $this->actingAs($jury);

        Livewire::test(CandidaturesListe::class)
            ->assertSee('Dossiers à évaluer')
            ->assertSee('Compléments')
            ->assertSee('Admis')
            ->assertSee('Refusés');
    }

    public function test_tableau_de_bord_calcule_les_indicateurs_et_decisions_par_programme(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $programme = $this->creerProgramme('Master Informatique');
        $this->creerCandidature(StatutCandidature::Soumise, $programme);
        $this->creerCandidature(StatutCandidature::TransmiseAuJury, $programme);
        $this->creerCandidature(StatutCandidature::Admise, $programme);
        $this->creerCandidature(StatutCandidature::Refusee, $programme);

        $reponse = $this->actingAs($agent)->get(route('dashboard'));

        $reponse->assertOk()
            ->assertSee('Répartition par statut')
            ->assertSee('Admissions et refus par programme')
            ->assertSee('Master Informatique');
        $this->assertSame(4, $reponse->viewData('nombreCandidatures'));
        $this->assertSame(1, $reponse->viewData('nombreEnAttenteJury'));
        $this->assertSame(2, $reponse->viewData('nombreDecisions'));
    }

    public function test_echec_email_ne_revient_pas_sur_la_transition_metier(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);

        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP indisponible'));

        $resultat = app(WorkflowCandidature::class)->demanderComplement(
            $candidature,
            $agent,
            'Merci de compléter le dossier.',
        );

        $this->assertSame(StatutCandidature::ComplementDemande, $resultat->statut);
        $this->assertDatabaseHas('emails_envoyes', [
            'candidature_id' => $candidature->id,
            'evenement' => 'complement_demande',
            'statut' => 'echec',
        ]);
    }

    private function utilisateurAvecRole(string $role): User
    {
        return $this->attribuerRole(User::factory()->create(), $role);
    }

    private function creerProgramme(string $nom = 'Programme workflow interface'): Programme
    {
        return Programme::create([
            'nom' => $nom.' '.fake()->unique()->numerify('###'),
            'niveau' => 'master',
            'date_ouverture' => now()->subMonth(),
            'date_fermeture' => now()->addMonth(),
            'actif' => true,
        ]);
    }

    private function creerCandidature(
        StatutCandidature $statut,
        ?Programme $programme = null,
    ): Candidature {
        $programme ??= $this->creerProgramme();
        $candidat = Candidat::create([
            'prenom' => fake()->firstName(),
            'nom' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
        ]);

        return Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => strtoupper(fake()->unique()->bothify('SGA-####-????')),
            'statut' => $statut,
            'soumise_le' => now(),
        ]);
    }

    private function ajouterDocumentValide(Candidature $candidature): DocumentCandidature
    {
        $type = TypeDocument::create([
            'code' => 'releve-'.$candidature->id,
            'nom' => 'Relevé de notes',
            'actif' => true,
        ]);
        $candidature->programme->typesDocuments()->attach($type->id, [
            'obligatoire' => true,
            'ordre' => 1,
        ]);

        return DocumentCandidature::create([
            'candidature_id' => $candidature->id,
            'type_document_id' => $type->id,
            'nom_original' => 'releve.pdf',
            'chemin_fichier' => 'tests/releve.pdf',
            'type_mime' => 'application/pdf',
            'taille_octets' => 1024,
            'statut' => 'valide',
        ]);
    }
}
