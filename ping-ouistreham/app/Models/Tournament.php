<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tournament extends Model
{
    protected $fillable = [
        'user_id', 'name', 'slug', 'date', 'location', 
        'latitude', 'longitude', 'contact_email', 
        'registration_deadline', 'max_points_allowed', 
        'is_published', 'status'
    ];

    // L'organisateur du tournoi
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function superTables(): HasMany
    {
        return $this->hasMany(SuperTable::class);
    }

    // Relation magique pour obtenir tous les inscrits du tournoi sans passer par les boucles
    public function registrations(): HasManyThrough
    {
        return $this->hasManyThrough(Registration::class, SubTable::class, 'super_table_id', 'sub_table_id');
        // Note: Cette relation nécessite que SubTable appartienne à SuperTable
    }
}