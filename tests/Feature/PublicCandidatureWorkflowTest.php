<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Livewire\Public\FormulaireCandidature;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Programme;
use App\Models\TypeDocument;
use App\Services\CandidatureSubmissionService;
use App\Services\CodeSuiviGenerator;
use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class PublicCandidatureWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_workflow_allows_candidate_to_view_programmes_and_submit_application(): void
    {
        $programme = Programme::create([
            'nom' => 'Stage de spécialisation',
            'niveau' => 'licence',
            'capacite_accueil' => 50,
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'frais_scolarite' => 1200,
            'echeancier_paiement' => 'Paiement unique',
            'description' => 'Programme de stage public.',
            'actif' => true,
        ]);

        $typeDocument = TypeDocument::create([
            'code' => 'cv',
            'nom' => 'CV',
            'description' => 'Curriculum Vitae',
            'actif' => true,
        ]);
        $programme->typesDocuments()->attach($typeDocument->id, ['obligatoire' => true]);

        $response = $this->get('/programmes');
        $response->assertStatus(200);
        $response->assertSee($programme->nom);

        $response = $this->get('/programmes/'.$programme->id);
        $response->assertStatus(200);
        $response->assertSee($programme->nom);
        $response->assertSee('Déposer ma candidature');

        $this->app->instance(CandidatureSubmissionService::class, new CandidatureSubmissionService(
            new CodeSuiviGenerator,
            new EmailService,
        ));

        $candidature = app(CandidatureSubmissionService::class)->soumettre(
            $programme,
            [
                'nom' => 'Diop',
                'prenom' => 'Awa',
                'date_naissance' => '1999-01-15',
                'email' => 'awa@example.com',
                'telephone' => '775000000',
                'pays' => 'Sénégal',
                'adresse' => 'Dakar',
                'derniere_formation' => 'Licence',
                'etablissement_origine' => 'Université X',
            ],
            [
                $typeDocument->id => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
            ]
        );

        $this->assertNotNull($candidature->code_suivi);

        $candidat = Candidat::query()->where('email', 'awa@example.com')->first();
        $this->assertNotNull($candidat);

        $candidature = Candidature::query()->where('candidat_id', $candidat->id)->where('programme_id', $programme->id)->first();
        $this->assertNotNull($candidature);
        $this->assertSame(StatutCandidature::Soumise, $candidature->statut);
        $this->assertNotNull($candidature->code_suivi);

        $this->get('/candidature/confirmation/'.$candidature->code_suivi)
            ->assertStatus(200)
            ->assertSee($candidature->code_suivi)
            ->assertSee($programme->nom);
    }

    public function test_livewire_submission_redirects_to_confirmation_and_persists_application(): void
    {
        $programme = Programme::create([
            'nom' => 'Cycle avancé',
            'niveau' => 'master',
            'capacite_accueil' => 30,
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'frais_scolarite' => 1500,
            'echeancier_paiement' => 'Paiement unique',
            'description' => 'Programme de test Livewire.',
            'actif' => true,
        ]);

        $component = Livewire::test(FormulaireCandidature::class, ['programme' => $programme])
            ->set('nom', 'Diop')
            ->set('prenom', 'Awa')
            ->set('date_naissance', '1999-01-15')
            ->set('email', 'awa-livewire@example.com')
            ->set('telephone', '775000000')
            ->set('pays', 'Sénégal')
            ->set('adresse', 'Dakar')
            ->set('derniere_formation', 'Licence')
            ->set('etablissement_origine', 'Université X');

        $component->call('soumettre');

        $component->assertHasNoErrors();
        $component->assertRedirectContains('/candidature/confirmation/');

        $this->assertDatabaseHas('candidatures', [
            'statut' => 'soumise',
            'code_suivi' => $component->instance()->confirmationCode,
        ]);
    }

    public function test_submission_proceeds_when_uploaded_file_is_invalid(): void
    {
        $programme = Programme::create([
            'nom' => 'Formation upload invalide',
            'niveau' => 'licence',
            'capacite_accueil' => 20,
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'frais_scolarite' => 800,
            'echeancier_paiement' => 'Paiement unique',
            'description' => 'Programme de test upload invalide.',
            'actif' => true,
        ]);

        $invalidFile = new UploadedFile(
            __DIR__.'/fixtures/does-not-exist.pdf',
            'does-not-exist.pdf',
            'application/pdf',
            null,
            true,
        );

        $candidature = app(CandidatureSubmissionService::class)->soumettre(
            $programme,
            [
                'nom' => 'Sow',
                'prenom' => 'Moussa',
                'date_naissance' => '1998-06-07',
                'email' => 'moussa@example.com',
                'telephone' => '775000001',
                'pays' => 'Sénégal',
                'adresse' => 'Thiès',
                'derniere_formation' => 'Bac',
                'etablissement_origine' => 'Lycée Z',
            ],
            [$invalidFile],
        );

        $this->assertSame(StatutCandidature::Soumise, $candidature->statut);
        $this->assertDatabaseHas('candidatures', [
            'id' => $candidature->id,
            'statut' => 'soumise',
        ]);
    }

    public function test_public_workflow_allows_candidate_to_save_draft(): void
    {
        $programme = Programme::create([
            'nom' => 'Formation temp',
            'niveau' => 'licence',
            'capacite_accueil' => 20,
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'frais_scolarite' => 800,
            'echeancier_paiement' => 'Paiement unique',
            'description' => 'Programme de test brouillon.',
            'actif' => true,
        ]);

        $this->app->instance(CandidatureSubmissionService::class, new CandidatureSubmissionService(
            new CodeSuiviGenerator,
            new EmailService,
        ));

        $candidature = app(CandidatureSubmissionService::class)->sauvegarderBrouillon(
            $programme,
            [
                'nom' => 'Dieng',
                'prenom' => 'Mamadou',
                'date_naissance' => '2000-03-10',
                'email' => 'mamadou@example.com',
                'telephone' => '776000000',
                'pays' => 'Sénégal',
                'adresse' => 'Dakar',
                'derniere_formation' => 'Bac',
                'etablissement_origine' => 'Lycée Y',
            ],
            null,
        );

        $this->assertSame(StatutCandidature::Brouillon, $candidature->statut);
        $this->assertNotNull($candidature->code_suivi);
    }
}
