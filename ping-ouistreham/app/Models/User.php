<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'license_number', // Ajouté
        'phone',          // Ajouté
        'points',         // Ajouté
        'club',           // Ajouté
        'role',           // Ajouté
        'coach_id',       // Ajouté
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
 * Les tableaux (séries) auxquels le joueur est inscrit.
 */
    public function subTables()
    {
        return $this->belongsToMany(SubTable::class)
                    ->withTimestamps(); // <--- AJOUTE CECI
    }

    public function users(): BelongsToMany
        {
            return $this->belongsToMany(User::class, 'sub_table_user')
                        ->withTimestamps();
        }

        /**
         * Relation vers le bloc horaire parent (SuperTable).
         */
    public function superTable(): BelongsTo
    {
        return $this->belongsTo(SuperTable::class);
    }

    // App\Models\User.php

    // Les joueurs dont cet utilisateur est responsable
    public function students()
    {
        return $this->hasMany(User::class, 'coach_id');
    }

    // L'entraîneur de cet utilisateur
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
