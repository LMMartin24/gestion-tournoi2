<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Registration; // Ajout de l'import pour la clarté
use App\Observers\RegistrationObserver; // Ajout de l'import
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Liaison de l'Observer pour gérer la liste d'attente automatique
        Registration::observe(RegistrationObserver::class);

        // 2. Définition de l'accès pour les coachs
        Gate::define('access-coach', function (User $user) {
            return $user->role === 'coach';
        });

        // 3. Définition de l'accès pour les admins
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}