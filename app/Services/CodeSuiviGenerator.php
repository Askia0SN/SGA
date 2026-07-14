<?php

namespace App\Services;

use App\Models\Candidature;
use Illuminate\Support\Str;

class CodeSuiviGenerator
{
    public function generer(): string
    {
        do {
            $partie = strtoupper(Str::random(4));
            $code = 'EPF-'.$partie.'-'.date('Y');
        } while (Candidature::query()->where('code_suivi', $code)->exists());

        return $code;
    }
}
