<?php

namespace Tests\Feature;

use App\Mail\CandidatureSoumiseMail;
use App\Models\Candidat;
use App\Models\Candidature;
use App\Models\Programme;
use App\Models\User;
use App\Notifications\InvitationCompteAdmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignEmailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_candidat_utilise_le_design_epf_et_affiche_les_reperes_du_dossier(): void
    {
        $programme = Programme::create([
            'nom' => 'Master Informatique',
            'niveau' => 'master',
            'date_ouverture' => now()->subDay(),
            'date_fermeture' => now()->addMonth(),
            'actif' => true,
        ]);
        $candidat = Candidat::create([
            'prenom' => 'Awa',
            'nom' => 'Ndiaye',
            'email' => 'awa.design@example.com',
        ]);
        $candidature = Candidature::create([
            'candidat_id' => $candidat->id,
            'programme_id' => $programme->id,
            'code_suivi' => 'EPF-DESIGN-2026',
            'statut' => 'soumise',
            'soumise_le' => now(),
        ]);

        $html = (new CandidatureSoumiseMail(
            $candidature,
            'Confirmation de candidature - EPF Africa',
            '<p>Bonjour Awa Ndiaye, votre candidature a bien été reçue.</p>',
            'Service Admission - EPF Africa',
        ))->render();

        $this->assertStringContainsString('Candidature reçue', $html);
        $this->assertStringContainsString('Master Informatique', $html);
        $this->assertStringContainsString('EPF-DESIGN-2026', $html);
        $this->assertStringContainsString('#d91426', $html);
        $this->assertStringContainsString('Suivre ma candidature', $html);
        $this->assertStringContainsString('alt="SGA EPF"', $html);
    }

    public function test_email_invitation_utilise_le_design_epf_et_le_bouton_d_activation(): void
    {
        $utilisateur = User::factory()->create([
            'name' => 'Membre Jury',
            'email' => 'jury.design@example.com',
        ]);

        $message = (new InvitationCompteAdmission('token-test', 'Admin EPF'))
            ->toMail($utilisateur);
        $html = (string) $message->render();

        $this->assertSame('Invitation à l’espace admission EPF', $message->subject);
        $this->assertStringContainsString('Bienvenue dans l’espace admission', $html);
        $this->assertStringContainsString('Définir mon mot de passe', $html);
        $this->assertStringContainsString('Admin EPF', $html);
        $this->assertStringContainsString('#6f22de', $html);
        $this->assertStringContainsString('alt="SGA EPF"', $html);
    }
}
