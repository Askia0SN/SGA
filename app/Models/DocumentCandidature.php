<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentCandidature extends Model
{
    protected $table = 'documents_candidature';

    protected $fillable = [
        'candidature_id',
        'type_document_id',
        'nom_original',
        'chemin_fichier',
        'type_mime',
        'taille_octets',
        'statut',
        'motif_rejet',
        'verifie_par',
        'verifie_le',
    ];

    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    public function typeDocument(): BelongsTo
    {
        return $this->belongsTo(TypeDocument::class);
    }

    public function verificateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifie_par');
    }

    protected function casts(): array
    {
        return [
            'taille_octets' => 'integer',
            'verifie_le' => 'datetime',
        ];
    }
}
