<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class SubTable extends Model
{
    use HasFactory;

    protected $fillable = ['super_table_id', 'label', 'entry_fee', 'points_max'];

    /**
     * Relation vers le SuperTable (le parent)
     */
    public function superTable()
    {
        return $this->belongsTo(SuperTable::class);
    }

    public function users(): BelongsToMany
    {
        // On lie les utilisateurs via la table pivot sub_table_user
        return $this->belongsToMany(User::class, 'sub_table_user')
                    ->withTimestamps();
    }
    

}