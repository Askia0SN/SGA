<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageCandidature extends Model
{
    protected $table = 'messages_candidature';

    protected $fillable = [
        'candidature_id',
        'user_id',
        'type',
        'visibilite',
        'contenu',
    ];

    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
