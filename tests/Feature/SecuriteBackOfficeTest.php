<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\InvitationCompteAdmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SecuriteBackOfficeTest extends TestCase
{
    use RefreshDatabase;

    public function test_aucune_route_d_inscription_publique_n_est_disponible(): void
    {
        $this->get('/admission/inscription')->assertNotFound();
        $this->post('/admission/inscription')->assertNotFound();
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }

    public function test_un_utilisateur_sans_role_ne_peut_pas_acceder_au_tableau_de_bord(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_un_compte_desactive_est_deconnecte_du_back_office(): void
    {
        $user = $this->attribuerRole(User::factory()->create(['actif' => false]));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_seul_le_super_administrateur_peut_gerer_les_utilisateurs(): void
    {
        $jury = $this->attribuerRole(User::factory()->create(), 'jury');
        $superAdmin = $this->attribuerRole(User::factory()->create(), 'super_admin');

        $this->actingAs($jury)
            ->get(route('utilisateurs.index'))
            ->assertForbidden();

        $this->actingAs($superAdmin)
            ->get(route('utilisateurs.index'))
            ->assertOk();
    }

    public function test_super_administrateur_peut_creer_et_inviter_un_utilisateur(): void
    {
        Notification::fake();

        $superAdmin = $this->attribuerRole(User::factory()->create(), 'super_admin');
        Role::firstOrCreate(['nom' => 'jury'], ['libelle' => 'Jury']);

        $this->actingAs($superAdmin)
            ->post(route('utilisateurs.store'), [
                'prenom' => 'Marie',
                'nom' => 'Diop',
                'email' => 'marie.diop@epf.fr',
                'role' => 'jury',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $utilisateur = User::where('email', 'marie.diop@epf.fr')->firstOrFail();

        $this->assertTrue($utilisateur->actif);
        $this->assertTrue($utilisateur->hasRole('jury'));
        $this->assertNull($utilisateur->email_verified_at);
        Notification::assertSentTo($utilisateur, InvitationCompteAdmission::class);
    }

    public function test_le_dernier_super_administrateur_ne_peut_pas_se_desactiver(): void
    {
        $superAdmin = $this->attribuerRole(User::factory()->create(), 'super_admin');

        $this->actingAs($superAdmin)
            ->patch(route('utilisateurs.update', $superAdmin), [
                'role' => 'super_admin',
                'actif' => false,
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($superAdmin->fresh()->actif);
    }

    public function test_commande_cree_le_premier_super_administrateur(): void
    {
        Role::create(['nom' => 'super_admin', 'libelle' => 'Super administrateur']);

        $this->artisan('sga:creer-super-admin', [
            '--prenom' => 'Aminata',
            '--nom' => 'Fall',
            '--email' => 'admin@epf.fr',
            '--password' => 'MotDePasse!2026',
        ])->assertSuccessful();

        $utilisateur = User::where('email', 'admin@epf.fr')->firstOrFail();

        $this->assertTrue($utilisateur->actif);
        $this->assertTrue($utilisateur->hasRole('super_admin'));
        $this->assertTrue($utilisateur->hasVerifiedEmail());
        $this->assertTrue(Hash::check('MotDePasse!2026', $utilisateur->password));
    }
}
