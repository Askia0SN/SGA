<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Livewire\Admission\CandidatureDetail;
use App\Livewire\Admission\CandidaturesListe;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\DocumentCandidature;
use App\Models\Programme;
use App\Models\TypeDocument;
use App\Models\User;
use App\Services\VerificationDocumentCandidature;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class GestionCandidaturesAdmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_admission_consulte_et_filtre_la_liste_des_candidatures(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidatureAwa = $this->creerCandidature(StatutCandidature::Soumise, 'Awa', 'Ndiaye');
        $candidatureMoussa = $this->creerCandidature(StatutCandidature::EnTraitementAdmission, 'Moussa', 'Fall');

        $this->actingAs($agent);

        Livewire::test(CandidaturesListe::class)
            ->assertSee($candidatureAwa->code_suivi)
            ->assertSee($candidatureMoussa->code_suivi)
            ->set('recherche', 'Awa')
            ->assertSee($candidatureAwa->code_suivi)
            ->assertDontSee($candidatureMoussa->code_suivi)
            ->set('recherche', '')
            ->set('statut', StatutCandidature::EnTraitementAdmission->value)
            ->assertDontSee($candidatureAwa->code_suivi)
            ->assertSee($candidatureMoussa->code_suivi);
    }

    public function test_jury_ne_voit_que_les_dossiers_qui_lui_sont_transmis(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $soumise = $this->creerCandidature(StatutCandidature::Soumise, 'Fatou', 'Diop');
        $transmise = $this->creerCandidature(StatutCandidature::TransmiseAuJury, 'Aminata', 'Ba');
        $complementAdmission = $this->creerCandidature(StatutCandidature::ComplementDemande, 'Lamine', 'Sarr');
        $complementJury = $this->creerCandidature(StatutCandidature::ComplementDemande, 'Sokhna', 'Faye');
        $complementJury->historiques()->create([
            'ancien_statut' => StatutCandidature::TransmiseAuJury->value,
            'nouveau_statut' => StatutCandidature::ComplementDemande->value,
            'acteur' => 'jury',
        ]);

        $this->actingAs($jury);

        Livewire::test(CandidaturesListe::class)
            ->assertDontSee($soumise->code_suivi)
            ->assertSee($transmise->code_suivi)
            ->assertDontSee($complementAdmission->code_suivi)
            ->assertSee($complementJury->code_suivi);
    }

    public function test_detail_du_dossier_respecte_les_autorisations_du_jury(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $soumise = $this->creerCandidature(StatutCandidature::Soumise);
        $transmise = $this->creerCandidature(StatutCandidature::TransmiseAuJury);

        $this->actingAs($jury)
            ->get(route('candidatures.show', $soumise))
            ->assertForbidden();

        $this->actingAs($jury)
            ->get(route('candidatures.show', $transmise))
            ->assertOk()
            ->assertSee($transmise->code_suivi);
    }

    public function test_service_admission_valide_un_document_depuis_le_dossier(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);
        $document = $this->ajouterDocument($candidature);

        $this->actingAs($agent);

        Livewire::test(CandidatureDetail::class, ['candidature' => $candidature])
            ->call('validerDocument', $document->id)
            ->assertHasNoErrors()
            ->assertSee('Le document a été validé.');

        $this->assertDatabaseHas('documents_candidature', [
            'id' => $document->id,
            'statut' => 'valide',
            'verifie_par' => $agent->id,
        ]);
        $this->assertDatabaseHas('journaux_actions', [
            'acteur_type' => User::class,
            'acteur_id' => $agent->id,
            'action' => 'document_candidature.valide',
            'cible_type' => DocumentCandidature::class,
            'cible_id' => $document->id,
        ]);
    }

    public function test_rejet_document_exige_un_motif_et_est_journalise(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);
        $document = $this->ajouterDocument($candidature);

        $this->actingAs($agent);

        Livewire::test(CandidatureDetail::class, ['candidature' => $candidature])
            ->call('preparerRejet', $document->id)
            ->call('rejeterDocument')
            ->assertHasErrors(['motifRejet' => 'required'])
            ->set('motifRejet', 'Le relevé doit contenir toutes les pages.')
            ->call('rejeterDocument')
            ->assertHasNoErrors()
            ->assertSee('Le document a été rejeté');

        $this->assertDatabaseHas('documents_candidature', [
            'id' => $document->id,
            'statut' => 'rejete',
            'motif_rejet' => 'Le relevé doit contenir toutes les pages.',
            'verifie_par' => $agent->id,
        ]);
        $this->assertDatabaseHas('journaux_actions', [
            'action' => 'document_candidature.rejete',
            'cible_id' => $document->id,
        ]);
    }

    public function test_jury_ne_peut_pas_verifier_un_document(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::TransmiseAuJury);
        $document = $this->ajouterDocument($candidature);

        $this->expectException(AuthorizationException::class);

        app(VerificationDocumentCandidature::class)->valider($document, $jury);
    }

    public function test_rejet_direct_sans_motif_est_refuse(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);
        $document = $this->ajouterDocument($candidature);

        $this->expectException(ValidationException::class);

        app(VerificationDocumentCandidature::class)->rejeter($document, $agent, '   ');
    }

    public function test_document_est_consulte_par_une_route_privee(): void
    {
        Storage::fake('local');
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);
        $document = $this->ajouterDocument($candidature);
        Storage::disk('local')->put($document->chemin_fichier, 'contenu-pdf');

        $this->get(route('documents.consulter', $document))
            ->assertRedirect(route('login'));

        $this->actingAs($agent)
            ->get(route('documents.consulter', $document))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('x-content-type-options', 'nosniff');
    }

    private function utilisateurAvecRole(string $role): User
    {
        return $this->attribuerRole(User::factory()->create(), $role);
    }

    private function creerCandidature(
        StatutCandidature $statut,
        string $prenom = 'Awa',
        string $nom = 'Ndiaye',
    ): Candidature {
        $programme = Programme::create([
            'nom' => 'Programme '.fake()->unique()->words(3, true),
            'niveau' => 'licence',
            'date_ouverture' => now()->subMonth(),
            'date_fermeture' => now()->addMonth(),
            'actif' => true,
        ]);

        $candidat = Candidat::create([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => fake()->unique()->safeEmail(),
            'telephone' => '+221 77 000 00 00',
        ]);

        return Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => strtoupper(fake()->unique()->bothify('SGA-####-????')),
            'statut' => $statut,
            'soumise_le' => now(),
        ]);
    }

    private function ajouterDocument(Candidature $candidature): DocumentCandidature
    {
        $typeDocument = TypeDocument::create([
            'code' => 'document-'.$candidature->id,
            'nom' => 'Relevé de notes',
            'actif' => true,
        ]);

        $candidature->programme->typesDocuments()->attach($typeDocument->id, [
            'obligatoire' => true,
            'ordre' => 1,
        ]);

        return DocumentCandidature::create([
            'candidature_id' => $candidature->id,
            'type_document_id' => $typeDocument->id,
            'nom_original' => 'releve-notes.pdf',
            'chemin_fichier' => 'candidatures/'.$candidature->id.'/releve-notes.pdf',
            'type_mime' => 'application/pdf',
            'taille_octets' => 1024,
            'statut' => 'en_attente',
        ]);
    }
}
