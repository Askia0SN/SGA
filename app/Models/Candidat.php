<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidat extends Model
{
    protected $fillable = [
        'prenom',
        'nom',
        'date_naissance',
        'email',
        'telephone',
        'pays',
        'adresse',
    ];

    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class);
    }

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }
}
