<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AgeMinimum implements ValidationRule
{
    public function __construct(private int $ageMinimum = 17) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value) {
            return;
        }

        $dateLimite = now()->subYears($this->ageMinimum)->startOfDay();

        if (strtotime($value) > $dateLimite->timestamp) {
            $fail("Vous devez avoir au moins {$this->ageMinimum} ans pour candidater.");
        }
    }
}
