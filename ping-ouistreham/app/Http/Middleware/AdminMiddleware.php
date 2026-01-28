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
        // On autorise l'admin de club ET le super_admin de la plateforme
        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Accès refusé. Réservé aux administrateurs.');
    }
}
