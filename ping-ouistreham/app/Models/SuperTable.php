<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SuperTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id', 
        'name', 
        'date', 
        'start_time', 
        'max_players', 
        'description',
        'is_locked' // Ajouté pour le verrouillage manuel
    ];

    /**
     * Casts des attributs.
     */
    protected $casts = [
        'is_locked' => 'boolean',
        'date' => 'date',
    ];

    /**
     * Relation avec le tournoi.
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Relation avec les sous-tables (tableaux de points).
     */
    public function subTables(): HasMany
    {
        return $this->hasMany(SubTable::class);
    }

    /**
     * Accès direct aux inscriptions via les sous-tables.
     */
    public function registrations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Registration::class, 
            SubTable::class,
            'super_table_id', // Clé étrangère sur SubTable
            'sub_table_id',   // Clé étrangère sur Registration
            'id',             // Clé locale sur SuperTable
            'id'              // Clé locale sur SubTable
        );
    }

    /**
     * Calcule le nombre total d'inscrits dans ce bloc (confirmés uniquement).
     */
    public function currentPlayersCount(): int
    {
        return $this->registrations()
            ->where('status', 'confirmed')
            ->count();
    }

    /**
     * Vérifie si le bloc est plein.
     */
    public function isFull(): bool
    {
        return $this->currentPlayersCount() >= (int) $this->max_players;
    }

    /**
     * Vérifie si le bloc est verrouillé par l'admin.
     */
    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }
}