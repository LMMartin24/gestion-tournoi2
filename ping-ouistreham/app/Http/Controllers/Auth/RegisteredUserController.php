<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Affiche la vue d'inscription.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Gère la requête d'inscription entrante.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation stricte des données saisies manuellement
        $request->validate([
            'role' => ['required', 'string', 'in:player,coach'],
            
            // La licence est unique en base pour éviter les doubles inscriptions
            'license_number' => $request->role === 'player' 
                ? ['required', 'string', 'max:20', 'unique:'.User::class] 
                : ['nullable', 'string', 'max:20'],
                
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            
            // 'name' est envoyé via le petit script JS (concaténation prénom + nom)
            'name' => ['required', 'string', 'max:255'],
            
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // On s'assure que le club est renseigné pour le suivi des tableaux
            'club' => ['required', 'string', 'max:255'],
            
            // Validation des points (minimum 500 pour le ping)
            'points' => $request->role === 'player'
                ? ['required', 'integer', 'min:500', 'max:4000']
                : ['nullable', 'integer'],
        ]);

        // 2. Création de l'utilisateur avec les données du formulaire
        $user = User::create([
            'role' => $request->role,
            'license_number' => $request->license_number,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'points' => $request->points ?? 0,
            'club' => $request->club,
            'phone' => $request->phone,
            // On peut ajouter ici une valeur par défaut si besoin
            'is_admin' => false, 
        ]);

        // 3. Déclenchement de l'événement de registration et connexion
        event(new Registered($user));

        Auth::login($user);

        // 4. Redirection vers le tableau de bord
        return redirect(route('dashboard', absolute: false));
    }
}