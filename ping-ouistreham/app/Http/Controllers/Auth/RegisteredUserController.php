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
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:player,coach'],
            // La licence est requise uniquement si le rôle est 'player'
            'license_number' => $request->role === 'player' 
                ? ['required', 'string', 'unique:'.User::class] 
                : ['nullable', 'string'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'points' => ['required', 'integer'],
            'club' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'role' => $request->role,
            'license_number' => $request->license_number,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'points' => $request->points,
            'club' => $request->club ?? 'Indépendant',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}