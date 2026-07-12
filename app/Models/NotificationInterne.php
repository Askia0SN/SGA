<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationInterne extends Model
{
    protected $table = 'notifications_internes';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'donnees',
        'lu_le',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'donnees' => 'array',
            'lu_le' => 'datetime',
        ];
    }
}
