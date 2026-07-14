<?php

namespace Tests\Feature;

use App\Enums\StatutCandidature;
use App\Livewire\Public\FormulaireCandidature;
use App\Mail\CandidatureSoumiseMail;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Programme;
use App\Models\TypeDocument;
use App\Services\CandidatureSubmissionService;
use App\Services\CodeSuiviGenerator;
use App\Services\EmailService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
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

    public function test_same_email_can_apply_to_two_different_programmes_without_overwriting_candidate(): void
    {
        Mail::fake();

        $premierProgramme = Programme::create($this->donneesProgramme('Master Informatique'));
        $secondProgramme = Programme::create($this->donneesProgramme('Master Energie'));
        $service = app(CandidatureSubmissionService::class);

        $premiere = $service->soumettre(
            $premierProgramme,
            $this->donneesCandidat('Awa', 'Ndiaye'),
            [],
        );
        $seconde = $service->soumettre(
            $secondProgramme,
            $this->donneesCandidat('Prenom modifie', 'Nom modifie'),
            [],
        );

        $this->assertSame($premiere->candidat_id, $seconde->candidat_id);
        $this->assertDatabaseCount('candidats', 1);
        $this->assertDatabaseCount('candidatures', 2);
        $this->assertDatabaseHas('candidats', [
            'id' => $premiere->candidat_id,
            'prenom' => 'Awa',
            'nom' => 'Ndiaye',
        ]);
    }

    public function test_existing_application_for_same_programme_is_rejected(): void
    {
        Mail::fake();

        $programme = Programme::create($this->donneesProgramme('Master Informatique'));
        $service = app(CandidatureSubmissionService::class);
        $service->soumettre($programme, $this->donneesCandidat(), []);

        $this->expectException(ValidationException::class);

        $service->soumettre($programme, $this->donneesCandidat(), []);
    }

    public function test_submitted_application_cannot_be_reopened_as_draft(): void
    {
        Mail::fake();

        $programme = Programme::create($this->donneesProgramme('Master Informatique'));
        $service = app(CandidatureSubmissionService::class);
        $candidature = $service->soumettre($programme, $this->donneesCandidat(), []);

        $this->expectException(ModelNotFoundException::class);

        $service->sauvegarderBrouillon(
            $programme,
            $this->donneesCandidat(),
            $candidature->id,
        );
    }

    public function test_livewire_candidate_id_is_locked(): void
    {
        $programme = Programme::create($this->donneesProgramme('Master Informatique'));

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(FormulaireCandidature::class, ['programme' => $programme])
            ->set('candidatureId', 123);
    }

    public function test_tracking_code_is_long_and_test_mail_route_is_not_exposed(): void
    {
        $code = app(CodeSuiviGenerator::class)->generer();

        $this->assertMatchesRegularExpression('/^EPF-[A-Z0-9]{12}$/', $code);
        $this->get('/send-mail')->assertNotFound();
    }

    public function test_draft_email_is_only_sent_when_draft_is_created(): void
    {
        Mail::fake();

        $programme = Programme::create($this->donneesProgramme('Master Informatique'));
        $service = app(CandidatureSubmissionService::class);
        $candidature = $service->sauvegarderBrouillon($programme, $this->donneesCandidat(), null);
        $service->sauvegarderBrouillon($programme, $this->donneesCandidat(), $candidature->id);

        Mail::assertSent(CandidatureSoumiseMail::class, 1);
    }

    public function test_submission_error_does_not_expose_technical_details(): void
    {
        $programme = Programme::create($this->donneesProgramme('Master Informatique'));
        $service = \Mockery::mock(CandidatureSubmissionService::class);
        $service->shouldReceive('soumettre')
            ->once()
            ->andThrow(new \RuntimeException('SQLSTATE information sensible'));
        $this->app->instance(CandidatureSubmissionService::class, $service);

        $component = Livewire::test(FormulaireCandidature::class, ['programme' => $programme]);
        foreach ($this->donneesCandidat() as $champ => $valeur) {
            if (property_exists($component->instance(), $champ)) {
                $component->set($champ, $valeur);
            }
        }

        $component->call('soumettre')
            ->assertHasErrors('workflow')
            ->assertSee('Veuillez réessayer')
            ->assertDontSee('SQLSTATE information sensible');
    }

    /** @return array<string, mixed> */
    private function donneesProgramme(string $nom): array
    {
        return [
            'nom' => $nom,
            'niveau' => 'master',
            'capacite_accueil' => 30,
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'description' => 'Programme de test.',
            'actif' => true,
        ];
    }

    /** @return array<string, mixed> */
    private function donneesCandidat(string $prenom = 'Awa', string $nom = 'Ndiaye'): array
    {
        return [
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => '1999-01-15',
            'email' => 'awa-multiprogramme@example.com',
            'telephone' => '775000000',
            'pays' => 'Sénégal',
            'adresse' => 'Dakar',
            'derniere_formation' => 'Licence',
            'etablissement_origine' => 'Université X',
        ];
    }
}
