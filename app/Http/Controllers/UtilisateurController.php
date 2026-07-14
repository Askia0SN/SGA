<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\InvitationCompteAdmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class UtilisateurController extends Controller
{
    private const ROLES_AUTORISES = ['super_admin', 'service_admission', 'jury'];

    public function index(): View
    {
        return view('utilisateurs.index', [
            'utilisateurs' => User::query()
                ->with('roles')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->paginate(20),
            'roles' => Role::query()
                ->whereIn('nom', self::ROLES_AUTORISES)
                ->orderBy('libelle')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(self::ROLES_AUTORISES)],
        ]);

        $role = Role::query()->where('nom', $donnees['role'])->firstOrFail();

        $utilisateur = DB::transaction(function () use ($donnees, $role): User {
            $utilisateur = User::create([
                'name' => trim($donnees['prenom'].' '.$donnees['nom']),
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'email' => Str::lower($donnees['email']),
                'telephone' => $donnees['telephone'] ?? null,
                'password' => Str::random(64),
                'actif' => true,
                'invite_le' => now(),
            ]);

            $utilisateur->roles()->attach($role);

            return $utilisateur;
        });

        return $this->envoyerInvitation($utilisateur, $request->user()->name)
            ? back()->with('status', 'Compte cree et invitation envoyee.')
            : back()->with('warning', 'Compte cree, mais l invitation n a pas pu etre envoyee. Vous pouvez la renvoyer.');
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $donnees = $request->validate([
            'role' => ['required', Rule::in(self::ROLES_AUTORISES)],
            'actif' => ['required', 'boolean'],
        ]);

        $actif = (bool) $donnees['actif'];

        if ($request->user()->is($utilisateur) && (! $actif || $donnees['role'] !== 'super_admin')) {
            throw ValidationException::withMessages([
                'role' => 'Vous ne pouvez pas retirer votre propre acces super administrateur.',
            ]);
        }

        $perdAccesSuperAdmin = $utilisateur->hasRole('super_admin')
            && (! $actif || $donnees['role'] !== 'super_admin');

        if ($perdAccesSuperAdmin && ! $this->autreSuperAdministrateurActif($utilisateur)) {
            throw ValidationException::withMessages([
                'role' => 'Au moins un super administrateur actif doit etre conserve.',
            ]);
        }

        $role = Role::query()->where('nom', $donnees['role'])->firstOrFail();

        DB::transaction(function () use ($utilisateur, $role, $actif): void {
            $utilisateur->update(['actif' => $actif]);
            $utilisateur->roles()->sync([$role->id]);
        });

        return back()->with('status', 'Acces utilisateur mis a jour.');
    }

    public function renvoyerInvitation(Request $request, User $utilisateur): RedirectResponse
    {
        $utilisateur->update(['invite_le' => now()]);

        return $this->envoyerInvitation($utilisateur, $request->user()->name)
            ? back()->with('status', 'Invitation renvoyee.')
            : back()->with('warning', 'L invitation n a pas pu etre envoyee. Verifiez la configuration email.');
    }

    private function envoyerInvitation(User $utilisateur, string $invitePar): bool
    {
        try {
            $token = Password::broker()->createToken($utilisateur);
            $utilisateur->notify(new InvitationCompteAdmission($token, $invitePar));

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    private function autreSuperAdministrateurActif(User $utilisateur): bool
    {
        return User::query()
            ->whereKeyNot($utilisateur->getKey())
            ->where('actif', true)
            ->whereHas('roles', fn ($query) => $query->where('nom', 'super_admin'))
            ->exists();
    }
}
