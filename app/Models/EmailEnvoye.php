<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailEnvoye extends Model
{
    protected $table = 'emails_envoyes';

    protected $fillable = [
        'candidature_id',
        'candidat_id',
        'user_id',
        'evenement',
        'destinataire_email',
        'objet',
        'contenu_html',
        'statut',
        'donnees',
        'message_erreur',
        'envoye_le',
    ];

    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'donnees' => 'array',
            'envoye_le' => 'datetime',
        ];
    }
}
