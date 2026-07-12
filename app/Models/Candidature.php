<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected function casts(): array
    {
        return [
            'soumise_le' => 'datetime',
            'transmise_au_jury_le' => 'datetime',
            'decision_le' => 'datetime',
        ];
    }
}
