<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreerSuperAdministrateur extends Command
{
    protected $signature = 'sga:creer-super-admin
                            {--prenom= : Prenom du super administrateur}
                            {--nom= : Nom du super administrateur}
                            {--email= : Adresse email professionnelle}
                            {--password= : Mot de passe initial}';

    protected $description = 'Cree le premier compte super administrateur du SGA';

    public function handle(): int
    {
        $role = Role::query()->where('nom', 'super_admin')->first();

        if (! $role) {
            $this->error('Le role super_admin est absent. Executez d abord php artisan migrate --seed.');

            return self::FAILURE;
        }

        $donnees = [
            'prenom' => $this->option('prenom') ?: $this->ask('Prenom'),
            'nom' => $this->option('nom') ?: $this->ask('Nom'),
            'email' => $this->option('email') ?: $this->ask('Email professionnel'),
            'password' => $this->option('password') ?: $this->secret('Mot de passe initial'),
        ];

        $validator = Validator::make($donnees, [
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                Password::min(12)->mixedCase()->letters()->numbers()->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $erreur) {
                $this->error($erreur);
            }

            return self::FAILURE;
        }

        $utilisateur = User::create([
            'name' => trim($donnees['prenom'].' '.$donnees['nom']),
            'prenom' => $donnees['prenom'],
            'nom' => $donnees['nom'],
            'email' => strtolower($donnees['email']),
            'password' => Hash::make($donnees['password']),
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        $utilisateur->roles()->attach($role);

        $this->info('Le compte super administrateur a ete cree pour '.$utilisateur->email.'.');

        return self::SUCCESS;
    }
}
