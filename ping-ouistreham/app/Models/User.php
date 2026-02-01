<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'license_number',
        'phone',
        'points',
        'club',
        'role',
        'coach_id',
        'password_plain',
        'is_verified_organizer' // Important pour ton flux de validation
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified_organizer' => 'boolean',
        ];
    }

    // --- RELATIONS JOUEUR ---

    /**
     * Les inscriptions réelles du joueur (avec scores, stats, etc.)
     */
    public function registrations(): HasMany
    {
        // On lie via l'utilisateur qui possède la licence
        // Dans ton cas, on peut lier par ID ou par license_number
        return $this->hasMany(Registration::class, 'user_id'); 
    }

    // --- RELATIONS COACH ---

    /**
     * Les joueurs rattachés à ce coach
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'coach_id');
    }

    /**
     * L'entraîneur du joueur
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
    

    // --- RELATIONS ORGANISATEUR (ADMIN CLUB) ---

    /**
     * Les tournois créés par cet utilisateur
     */
    public function managedTournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'user_id');
    }

    // --- HELPERS DE RÔLES (Pratique pour les Controllers & Blade) ---

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCoach(): bool
    {
        return $this->role === 'coach';
    }
}