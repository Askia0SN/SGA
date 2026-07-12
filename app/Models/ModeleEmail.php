<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeleEmail extends Model
{
    protected $table = 'modeles_emails';

    protected $fillable = [
        'evenement',
        'objet',
        'contenu_html',
        'signature',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }
}
