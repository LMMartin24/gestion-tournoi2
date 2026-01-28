<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubTable extends Model
{
    use HasFactory;

    // Ajout de points_min pour la cohérence avec ta vision nationale
    protected $fillable = ['super_table_id', 'label', 'entry_fee', 'points_min', 'points_max'];

    /**
     * Relation vers le bloc horaire (SuperTable)
     */
    public function superTable(): BelongsTo
    {
        return $this->belongsTo(SuperTable::class);
    }

    /**
     * Relation vers les inscriptions.
     * On utilise HasMany vers Registration car on a besoin des données 
     * spécifiques stockées dans la table pivot (points, licence, etc.)
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Helper pour savoir combien de joueurs sont confirmés dans ce tableau précis
     */
    public function confirmedPlayersCount(): int
    {
        return $this->registrations()->where('status', 'confirmed')->count();
    }
}