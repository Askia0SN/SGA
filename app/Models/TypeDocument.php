<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeDocument extends Model
{
    protected $table = 'types_documents';

    protected $fillable = [
        'code',
        'nom',
        'description',
        'actif',
    ];

    public function programmes(): BelongsToMany
    {
        return $this->belongsToMany(Programme::class, 'programme_type_document')
            ->withPivot(['obligatoire', 'ordre'])
            ->withTimestamps();
    }

    public function documentsCandidature(): HasMany
    {
        return $this->hasMany(DocumentCandidature::class);
    }

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }
}
