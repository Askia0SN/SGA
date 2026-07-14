<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function attribuerRole(User $user, string $nom = 'service_admission'): User
    {
        $role = Role::firstOrCreate(
            ['nom' => $nom],
            ['libelle' => str_replace('_', ' ', ucfirst($nom))],
        );

        $user->roles()->syncWithoutDetaching([$role->id]);

        return $user;
    }
}
