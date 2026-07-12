<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueCandidature extends Model
{
    protected $table = 'historiques_candidature';

    public const CREATED_AT = 'cree_le';

    public const UPDATED_AT = null;

    protected $fillable = [
        'candidature_id',
        'ancien_statut',
        'nouveau_statut',
        'modifie_par',
        'acteur',
        'commentaire',
    ];

    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifie_par');
    }

    protected function casts(): array
    {
        return [
            'cree_le' => 'datetime',
        ];
    }
}
