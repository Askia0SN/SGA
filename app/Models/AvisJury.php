<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvisJury extends Model
{
    protected $table = 'avis_jury';

    protected $fillable = [
        'candidature_id',
        'jury_id',
        'decision',
        'commentaire',
        'decide_le',
    ];

    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    public function jury(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jury_id');
    }

    protected function casts(): array
    {
        return [
            'decide_le' => 'datetime',
        ];
    }
}
