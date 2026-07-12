<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'nom',
        'email',
        'telephone',
        'password',
        'actif',
        'invite_le',
        'derniere_connexion_le',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'utilisateur_role');
    }

    public function candidaturesTransmises(): HasMany
    {
        return $this->hasMany(Candidature::class, 'transmise_par');
    }

    public function decisionsCandidature(): HasMany
    {
        return $this->hasMany(Candidature::class, 'decision_par');
    }

    public function avisJury(): HasMany
    {
        return $this->hasMany(AvisJury::class, 'jury_id');
    }

    public function documentsVerifies(): HasMany
    {
        return $this->hasMany(DocumentCandidature::class, 'verifie_par');
    }

    public function messagesCandidature(): HasMany
    {
        return $this->hasMany(MessageCandidature::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'email_verified_at' => 'datetime',
            'invite_le' => 'datetime',
            'derniere_connexion_le' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
