<?php

namespace App\Policies;

use App\Enums\StatutCandidature;
use App\Models\DocumentCandidature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DocumentCandidaturePolicy
{
    public function before(User $user): ?bool
    {
        return $user->actif ? null : false;
    }

    public function view(User $user, DocumentCandidature $document): bool
    {
        return Gate::forUser($user)->allows('view', $document->candidature);
    }

    public function verifier(User $user, DocumentCandidature $document): bool
    {
        return $user->hasAnyRole(['super_admin', 'service_admission'])
            && $document->candidature->statut === StatutCandidature::EnTraitementAdmission;
    }
}
