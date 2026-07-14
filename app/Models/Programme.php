<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Programme extends Model
{
    protected $fillable = [
        'nom',
        'niveau',
        'capacite_accueil',
        'date_ouverture',
        'date_fermeture',
        'frais_scolarite',
        'echeancier_paiement',
        'description',
        'actif',
    ];

    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class);
    }

    public function typesDocuments(): BelongsToMany
    {
        return $this->belongsToMany(TypeDocument::class, 'programme_type_document')
            ->withPivot(['obligatoire', 'ordre'])
            ->withTimestamps()
            ->orderByPivot('ordre');
    }

    public function estOuvertAuxCandidatures(): bool
    {
        $aujourdhui = now()->startOfDay();

        return $this->actif
            && $this->date_ouverture->lte($aujourdhui)
            && $this->date_fermeture->gte($aujourdhui);
    }

    public function libelleNiveau(): string
    {
        return match ($this->niveau) {
            'classe_preparatoire' => 'Classe préparatoire',
            'licence' => 'Licence',
            'master' => 'Master',
            default => ucfirst($this->niveau),
        };
    }

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'capacite_accueil' => 'integer',
            'date_ouverture' => 'date',
            'date_fermeture' => 'date',
            'frais_scolarite' => 'decimal:2',
        ];
    }
}
