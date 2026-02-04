<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuperTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id', 
        'name', 
        'date', 
        'start_time', 
        'max_players', 
        'description'
    ];
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function subTables(): HasMany
    {
        return $this->hasMany(SubTable::class);
    }

    /**
     * Calcule le nombre total d'inscrits dans ce bloc (tous tableaux confondus)
     */
    public function currentPlayersCount(): int
    {
        // On compte les inscriptions confirmées dans toutes les sous-tables de cette SuperTable
        return Registration::whereIn('sub_table_id', $this->subTables()->pluck('id'))
            ->where('status', 'confirmed')
            ->count();
    }

    /**
     * Vérifie si le bloc est plein
     */
    public function isFull()
    {
        $confirmedCount = \App\Models\Registration::whereHas('subTable', function($q) {
            $q->where('super_table_id', $this->id);
        })->where('status', 'confirmed')->count();

        return $confirmedCount >= (int) $this->max_players;
    }

    // app/Models/SuperTable.php

    public function registrations()
    {
        // Si tes inscriptions sont liées aux SubTables, 
        // on utilise hasManyThrough pour y accéder depuis la SuperTable
        return $this->hasManyThrough(
            \App\Models\Registration::class, 
            \App\Models\SubTable::class,
            'super_table_id', // Clé étrangère sur SubTable
            'sub_table_id',   // Clé étrangère sur Registration
            'id',             // Clé locale sur SuperTable
            'id'              // Clé locale sur SubTable
        );
    }

    
}