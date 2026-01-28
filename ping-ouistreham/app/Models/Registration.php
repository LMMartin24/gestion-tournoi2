<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'created_by', 
        'sub_table_id', 
        'player_license', 
        'player_firstname', 
        'player_lastname', 
        'player_points', 
        'priority', 
        'status', 
        'presence_confirmed'
    ];

    /**
     * Relation vers le créateur (Le compte qui a fait l'action)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation vers le tableau technique
     */
    public function subTable(): BelongsTo
    {
        return $this->belongsTo(SubTable::class);
    }

    /**
     * Accès direct au bloc horaire (SuperTable) via la SubTable
     */
    public function superTable()
    {
        return $this->subTable->superTable();
    }

    /**
     * Scope pour filtrer facilement les confirmations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope pour la liste d'attente
     */
    public function scopeInWaitingList($query)
    {
        return $query->where('status', 'waiting_list');
    }
}