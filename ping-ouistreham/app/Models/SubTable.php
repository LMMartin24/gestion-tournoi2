<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}