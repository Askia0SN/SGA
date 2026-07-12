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
