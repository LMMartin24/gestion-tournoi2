<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil dynamique avec le prochain tournoi et les stats.
     */
    public function index()
    {
        // 1. Récupérer le prochain tournoi (Hero Section)
        // Note: On retire 'is_published' si tu n'as pas encore ajouté la colonne en BDD
        $nextTournament = Tournament::where('status', 'accepted')
            ->where('date', '>=', now()->startOfDay())
            ->with(['superTables.subTables.registrations']) // Optimise les compteurs
            ->orderBy('date', 'asc')
            ->first();

        // 2. Récupérer les tournois suivants (Calendrier)
        $upcomingTournaments = Tournament::where('status', 'accepted')
            ->where('date', '>', optional($nextTournament)->date ?? now())
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        // 3. Préparer les statistiques globales
        $stats = [
            'total_registrations' => Registration::count(),
            'total_clubs' => User::whereNotNull('club')->distinct('club')->count(),
            'total_tournaments' => Tournament::where('status', 'accepted')->count(),
        ];

        // 4. On renvoie tout à la vue welcome
        return view('welcome', [
            'nextTournament' => $nextTournament,
            'upcomingTournaments' => $upcomingTournaments, // Variable utilisée dans ton @foreach final
            'stats' => $stats
        ]);
    }
}