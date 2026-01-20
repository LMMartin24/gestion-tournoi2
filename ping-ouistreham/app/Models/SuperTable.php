<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperTable extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'tournament_id',
        'name',
        'start_time',
        'max_players',
    ];

    /**
     * Relation avec le tournoi
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Relation avec les sous-tableaux (SubTables)
     */
    public function subTables()
    {
        return $this->hasMany(SubTable::class);
    }


    
}