<?php

namespace App\Models;

use App\Enums\StatutCandidature;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Candidature extends Model
{
    protected $fillable = [
        'candidat_id',
        'programme_id',
        'code_suivi',
        'statut',
        'derniere_formation',
        'etablissement_origine',
        'lettre_motivation',
        'commentaire_interne',
        'transmise_par',
        'decision_par',
        'soumise_le',
        'transmise_au_jury_le',
        'decision_le',
    ];

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    public function programme(): BelongsTo
    {
        return $this->belongsTo(Programme::class);
    }

    public function agentTransmission(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transmise_par');
    }

    public function auteurDecision(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decision_par');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DocumentCandidature::class);
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueCandidature::class)->orderBy('cree_le');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MessageCandidature::class);
    }

    public function avisJury(): HasMany
    {
        return $this->hasMany(AvisJury::class);
    }

    public function emailsEnvoyes(): HasMany
    {
        return $this->hasMany(EmailEnvoye::class);
    }

    public function dossierEstComplet(): bool
    {
        return $this->documentsObligatoiresNonValides()->isEmpty();
    }

    /**
     * Limite les dossiers aux candidatures que l'utilisateur peut consulter.
     */
    public function scopeVisiblePour(Builder $query, User $utilisateur): Builder
    {
        if ($utilisateur->hasAnyRole(['super_admin', 'service_admission'])) {
            return $query;
        }

        if (! $utilisateur->hasRole('jury')) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $requete): void {
            $requete
                ->whereIn('statut', [
                    StatutCandidature::TransmiseAuJury->value,
                    StatutCandidature::Admise->value,
                    StatutCandidature::Refusee->value,
                ])
                ->orWhere(function (Builder $complements): void {
                    $complements
                        ->where('statut', StatutCandidature::ComplementDemande->value)
                        ->whereHas('historiques', function (Builder $historique): void {
                            $historique
                                ->where('ancien_statut', StatutCandidature::TransmiseAuJury->value)
                                ->where('nouveau_statut', StatutCandidature::ComplementDemande->value);
                        });
                });
        });
    }

    /**
     * @return Collection<int, TypeDocument>
     */
    public function documentsObligatoiresNonValides(): Collection
    {
        $this->loadMissing(['programme.typesDocuments', 'documents']);

        $typesValides = $this->documents
            ->where('statut', 'valide')
            ->pluck('type_document_id')
            ->filter()
            ->unique();

        return $this->programme->typesDocuments
            ->filter(fn (TypeDocument $typeDocument) => (bool) $typeDocument->pivot->obligatoire)
            ->reject(fn (TypeDocument $typeDocument) => $typesValides->contains($typeDocument->id))
            ->values();
    }

    protected function casts(): array
    {
        return [
            'statut' => StatutCandidature::class,
            'soumise_le' => 'datetime',
            'transmise_au_jury_le' => 'datetime',
            'decision_le' => 'datetime',
        ];
    }
}
