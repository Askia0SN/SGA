<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Livewire\Admission\CandidaturesListe;
use App\Livewire\Public\FormulaireCandidature;
use App\Mail\CandidatureSoumiseMail;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Programme;
use App\Models\TypeDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ParcoursCandidatureCompletTest extends TestCase
{
    use RefreshDatabase;

    public function test_soumission_candidat_apparait_dans_le_back_office_et_declenche_un_email(): void
    {
        Mail::fake();

        $programme = Programme::create([
            'nom' => 'Master Informatique',
            'niveau' => 'master',
            'capacite_accueil' => 30,
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'description' => 'Programme de test du parcours complet.',
            'actif' => true,
        ]);

        $formulaire = Livewire::test(FormulaireCandidature::class, ['programme' => $programme])
            ->set('nom', 'Ndiaye')
            ->set('prenom', 'Awa')
            ->set('date_naissance', '1999-01-15')
            ->set('email', 'awa.parcours@example.com')
            ->set('telephone', '775000000')
            ->set('pays', 'Sénégal')
            ->set('adresse', 'Dakar')
            ->set('derniere_formation', 'Licence informatique')
            ->set('etablissement_origine', 'Université de Dakar')
            ->call('soumettre')
            ->assertHasNoErrors()
            ->assertRedirectContains('/candidature/confirmation/');

        $candidature = Candidature::query()
            ->where('code_suivi', $formulaire->instance()->confirmationCode)
            ->firstOrFail();

        $this->assertSame(StatutCandidature::Soumise, $candidature->statut);
        $this->assertDatabaseHas('emails_envoyes', [
            'candidature_id' => $candidature->id,
            'evenement' => 'candidature_soumise',
            'statut' => 'envoye',
        ]);
        Mail::assertSent(CandidatureSoumiseMail::class, 1);

        $agent = $this->attribuerRole(User::factory()->create(), 'service_admission');
        $this->actingAs($agent)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Awa Ndiaye')
            ->assertSee($candidature->code_suivi);

        Livewire::actingAs($agent)
            ->test(CandidaturesListe::class)
            ->assertSee('Awa Ndiaye')
            ->assertSee($candidature->code_suivi)
            ->assertSee('Candidature soumise');
    }

    public function test_candidat_transmet_un_complement_qui_revient_au_service_admission(): void
    {
        Storage::fake('local');

        $programme = Programme::create([
            'nom' => 'Master Energie',
            'niveau' => 'master',
            'capacite_accueil' => 20,
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'description' => 'Programme de test des compléments.',
            'actif' => true,
        ]);
        $typeDocument = TypeDocument::create([
            'code' => 'releve-notes',
            'nom' => 'Relevé de notes',
            'actif' => true,
        ]);
        $programme->typesDocuments()->attach($typeDocument, [
            'obligatoire' => true,
            'ordre' => 1,
        ]);

        $candidat = Candidat::create([
            'prenom' => 'Moussa',
            'nom' => 'Diop',
            'email' => 'moussa.complement@example.com',
        ]);
        $candidature = Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => 'EPF-COMPLEMENT12',
            'statut' => StatutCandidature::ComplementDemande,
            'soumise_le' => now(),
        ]);
        $candidature->messages()->create([
            'type' => 'demande_complement',
            'visibilite' => 'candidat',
            'contenu' => 'Merci de transmettre votre relevé de notes.',
        ]);
        $agent = $this->attribuerRole(User::factory()->create(), 'service_admission');

        $response = $this->post(route('candidatures.complement.envoyer'), [
            'code_suivi' => $candidature->code_suivi,
            'email' => $candidat->email,
            'message' => 'Voici le document demandé.',
            'documents' => [
                $typeDocument->id => UploadedFile::fake()->create('releve.pdf', 100, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('candidatures.suivi'));
        $this->assertSame(StatutCandidature::EnTraitementAdmission, $candidature->fresh()->statut);
        $this->assertDatabaseHas('documents_candidature', [
            'candidature_id' => $candidature->id,
            'type_document_id' => $typeDocument->id,
            'statut' => 'en_attente',
        ]);
        $this->assertDatabaseHas('messages_candidature', [
            'candidature_id' => $candidature->id,
            'type' => 'message_candidat',
            'contenu' => 'Voici le document demandé.',
        ]);
        $this->assertDatabaseHas('historiques_candidature', [
            'candidature_id' => $candidature->id,
            'ancien_statut' => StatutCandidature::ComplementDemande->value,
            'nouveau_statut' => StatutCandidature::EnTraitementAdmission->value,
            'acteur' => 'candidat',
        ]);
        $this->assertDatabaseHas('notifications_internes', [
            'user_id' => $agent->id,
            'type' => 'complement_candidat_recu',
        ]);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Votre complément a été transmis')
            ->assertSee('En cours d’étude')
            ->assertSee('Votre réponse');
    }

    public function test_complement_est_refuse_si_email_ne_correspond_pas_au_dossier(): void
    {
        $programme = Programme::create([
            'nom' => 'Licence Informatique',
            'niveau' => 'licence',
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'actif' => true,
        ]);
        $candidat = Candidat::create([
            'prenom' => 'Awa',
            'nom' => 'Fall',
            'email' => 'awa.fall@example.com',
        ]);
        $candidature = Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => 'EPF-SECURITE123',
            'statut' => StatutCandidature::ComplementDemande,
        ]);

        $this->from(route('candidatures.suivi'))
            ->post(route('candidatures.complement.envoyer'), [
                'code_suivi' => $candidature->code_suivi,
                'email' => 'autre@example.com',
                'message' => 'Tentative non autorisée.',
            ])
            ->assertRedirect(route('candidatures.suivi'))
            ->assertSessionHasErrors('code_suivi');

        $this->assertSame(StatutCandidature::ComplementDemande, $candidature->fresh()->statut);
        $this->assertDatabaseMissing('messages_candidature', [
            'candidature_id' => $candidature->id,
            'type' => 'message_candidat',
        ]);
    }
}
