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
    public function isFull(): bool
    {
        return $this->currentPlayersCount() >= $this->max_players;
    }

    
}