<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalAction extends Model
{
    protected $table = 'journaux_actions';

    public const CREATED_AT = 'cree_le';

    public const UPDATED_AT = null;

    protected $fillable = [
        'acteur_type',
        'acteur_id',
        'action',
        'cible_type',
        'cible_id',
        'anciennes_valeurs',
        'nouvelles_valeurs',
        'adresse_ip',
        'user_agent',
    ];

    public function acteur(): MorphTo
    {
        return $this->morphTo();
    }

    public function cible(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'anciennes_valeurs' => 'array',
            'nouvelles_valeurs' => 'array',
            'cree_le' => 'datetime',
        ];
    }
}
