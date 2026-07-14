<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\DocumentCandidature;
use App\Models\Programme;
use App\Models\TypeDocument;
use App\Models\User;
use App\Services\WorkflowCandidature;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WorkflowCandidatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_admission_peut_prendre_en_charge_une_candidature_soumise(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::Soumise);

        $resultat = app(WorkflowCandidature::class)->prendreEnCharge($candidature, $agent);

        $this->assertSame(StatutCandidature::EnTraitementAdmission, $resultat->statut);
        $this->assertDatabaseHas('historiques_candidature', [
            'candidature_id' => $candidature->id,
            'ancien_statut' => StatutCandidature::Soumise->value,
            'nouveau_statut' => StatutCandidature::EnTraitementAdmission->value,
            'modifie_par' => $agent->id,
            'acteur' => 'service_admission',
        ]);
    }

    public function test_jury_ne_peut_pas_prendre_en_charge_un_dossier_a_la_place_du_service_admission(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::Soumise);

        $this->expectException(AuthorizationException::class);

        app(WorkflowCandidature::class)->prendreEnCharge($candidature, $jury);
    }

    public function test_demande_de_complement_est_historisee_et_visible_du_candidat(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);

        $resultat = app(WorkflowCandidature::class)->demanderComplement(
            $candidature,
            $agent,
            'Merci de fournir le releve de notes complet.',
        );

        $this->assertSame(StatutCandidature::ComplementDemande, $resultat->statut);
        $this->assertDatabaseHas('messages_candidature', [
            'candidature_id' => $candidature->id,
            'user_id' => $agent->id,
            'type' => 'demande_complement',
            'visibilite' => 'candidat',
        ]);
    }

    public function test_jury_ne_voit_un_complement_que_si_le_dossier_lui_a_deja_ete_transmis(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::ComplementDemande);

        $this->assertFalse(Gate::forUser($jury)->allows('view', $candidature));

        $candidature->historiques()->create([
            'ancien_statut' => StatutCandidature::TransmiseAuJury->value,
            'nouveau_statut' => StatutCandidature::ComplementDemande->value,
            'acteur' => 'jury',
        ]);

        $this->assertTrue(Gate::forUser($jury)->allows('view', $candidature));
    }

    public function test_dossier_incomplet_ne_peut_pas_etre_transmis_au_jury(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);
        $this->ajouterDocumentObligatoire($candidature, 'en_attente');

        try {
            app(WorkflowCandidature::class)->transmettreAuJury($candidature, $agent);
            $this->fail('La transmission aurait du etre bloquee.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('documents', $exception->errors());
        }

        $this->assertSame(StatutCandidature::EnTraitementAdmission, $candidature->fresh()->statut);
        $this->assertDatabaseMissing('historiques_candidature', [
            'candidature_id' => $candidature->id,
            'nouveau_statut' => StatutCandidature::TransmiseAuJury->value,
        ]);
    }

    public function test_dossier_complet_peut_etre_transmis_au_jury(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::EnTraitementAdmission);
        $this->ajouterDocumentObligatoire($candidature, 'valide');

        $resultat = app(WorkflowCandidature::class)->transmettreAuJury($candidature, $agent);

        $this->assertSame(StatutCandidature::TransmiseAuJury, $resultat->statut);
        $this->assertSame($agent->id, $resultat->transmise_par);
        $this->assertNotNull($resultat->transmise_au_jury_le);
    }

    public function test_seul_le_jury_peut_prendre_une_decision_finale(): void
    {
        $agent = $this->utilisateurAvecRole('service_admission');
        $candidature = $this->creerCandidature(StatutCandidature::TransmiseAuJury);

        $this->expectException(AuthorizationException::class);

        app(WorkflowCandidature::class)->decider(
            $candidature,
            $agent,
            StatutCandidature::Admise,
        );
    }

    public function test_jury_peut_admettre_une_candidature_transmise(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::TransmiseAuJury);

        $resultat = app(WorkflowCandidature::class)->decider(
            $candidature,
            $jury,
            StatutCandidature::Admise,
            'Avis favorable.',
        );

        $this->assertSame(StatutCandidature::Admise, $resultat->statut);
        $this->assertSame($jury->id, $resultat->decision_par);
        $this->assertNotNull($resultat->decision_le);
        $this->assertDatabaseHas('avis_jury', [
            'candidature_id' => $candidature->id,
            'jury_id' => $jury->id,
            'decision' => 'admettre',
        ]);
    }

    public function test_un_refus_sans_motif_est_interdit(): void
    {
        $jury = $this->utilisateurAvecRole('jury');
        $candidature = $this->creerCandidature(StatutCandidature::TransmiseAuJury);

        $this->expectException(ValidationException::class);

        app(WorkflowCandidature::class)->decider(
            $candidature,
            $jury,
            StatutCandidature::Refusee,
        );
    }

    public function test_dossier_finalise_ne_peut_plus_changer_de_statut(): void
    {
        $superAdmin = $this->utilisateurAvecRole('super_admin');
        $candidature = $this->creerCandidature(StatutCandidature::Admise);

        $this->expectException(AuthorizationException::class);

        app(WorkflowCandidature::class)->demanderComplement(
            $candidature,
            $superAdmin,
            'Tentative de reouverture.',
        );
    }

    private function utilisateurAvecRole(string $role): User
    {
        return $this->attribuerRole(User::factory()->create(), $role);
    }

    private function creerCandidature(StatutCandidature $statut): Candidature
    {
        $programme = Programme::create([
            'nom' => 'Programme workflow '.$statut->value,
            'niveau' => 'licence',
            'date_ouverture' => now()->subMonth(),
            'date_fermeture' => now()->addMonth(),
            'actif' => true,
        ]);

        $candidat = Candidat::create([
            'prenom' => 'Awa',
            'nom' => 'Ndiaye',
            'email' => fake()->unique()->safeEmail(),
        ]);

        return Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => strtoupper(fake()->bothify('SGA-####-????')),
            'statut' => $statut,
            'soumise_le' => now(),
        ]);
    }

    private function ajouterDocumentObligatoire(Candidature $candidature, string $statut): void
    {
        $typeDocument = TypeDocument::create([
            'code' => 'document-'.$candidature->id,
            'nom' => 'Releve de notes',
            'actif' => true,
        ]);

        $candidature->programme->typesDocuments()->attach($typeDocument->id, [
            'obligatoire' => true,
            'ordre' => 1,
        ]);

        DocumentCandidature::create([
            'candidature_id' => $candidature->id,
            'type_document_id' => $typeDocument->id,
            'nom_original' => 'releve.pdf',
            'chemin_fichier' => 'tests/releve.pdf',
            'type_mime' => 'application/pdf',
            'taille_octets' => 1024,
            'statut' => $statut,
        ]);
    }
}
