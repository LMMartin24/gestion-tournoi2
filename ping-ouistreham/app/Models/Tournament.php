<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Tournament extends Model
{
protected $fillable = [
    'user_id', 'name', 'slug', 'date', 'location', 
    'contact_email', 'registration_deadline', 
    'max_points_allowed', 'is_published', 'status',
    'latitude', 'longitude', 'description', 'status' // Ajoute ceux-là si ils sont dans ta table
];

    /**
     * Boot function pour gérer la logique automatique.
     */
    protected static function boot()
    {
        parent::boot();

        // Génération automatique du slug avant la création en base
        static::creating(function ($tournament) {
            if (empty($tournament->slug)) {
                $tournament->slug = Str::slug($tournament->name) . '-' . rand(1000, 9999);
            }
        });
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function superTables(): HasMany
    {
        return $this->hasMany(SuperTable::class);
    }

    public function registrations(): HasManyThrough
    {
        // On récupère les inscriptions à travers les séries (SubTables)
        return $this->hasManyThrough(Registration::class, SubTable::class);
    }
    // Dans app/Models/Tournament.php
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
}