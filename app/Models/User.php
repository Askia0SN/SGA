<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmail, Notifiable;

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
        'email_verified_at',
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

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('nom', $role)->exists();
    }

    /**
     * @param  array<int, string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('nom', $roles)->exists();
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

    public function notificationsInternes(): HasMany
    {
        return $this->hasMany(NotificationInterne::class);
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
