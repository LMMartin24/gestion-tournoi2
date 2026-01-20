<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // On vérifie si l'utilisateur est connecté ET si son rôle est 'admin'
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Sinon, on redirige vers le dashboard avec un message d'erreur
        return redirect('/dashboard')->with('error', 'Accès refusé. Réservé aux administrateurs.');
    }
}
