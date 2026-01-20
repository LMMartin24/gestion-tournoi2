<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = ['name', 'date', 'location', 'is_active'];

    public function superTables() 
    {
        return $this->hasMany(SuperTable::class);
    }
}