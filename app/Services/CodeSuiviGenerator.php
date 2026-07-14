<?php

namespace App\Services;

use App\Models\Candidature;
use Illuminate\Support\Str;

class CodeSuiviGenerator
{
    public function generer(): string
    {
        do {
            $code = 'EPF-'.Str::upper(Str::random(12));
        } while (Candidature::query()->where('code_suivi', $code)->exists());

        return $code;
    }
}
