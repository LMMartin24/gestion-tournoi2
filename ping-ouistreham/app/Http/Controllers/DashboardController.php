<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubTable;

class DashboardController extends Controller
{
    // DashboardController.php

    public function index()
    {
        $user = Auth::user();

        // On charge les sous-tableaux ET leurs utilisateurs pour recalculer la somme à chaque fois
        $mySubTables = $user->subTables()
            ->with(['superTable.subTables.users']) 
            ->get();

        $availableSubTables = SubTable::where('points_max', '>=', $user->points)
            ->whereDoesntHave('users', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with(['superTable.subTables.users']) // Indispensable pour la jauge
            ->get();

        $totalToPay = $mySubTables->sum('entry_fee');

        return view('dashboard', compact('mySubTables', 'availableSubTables', 'totalToPay'));
    }

    public function register(SubTable $subTable)
    {
        $user = Auth::user();

        // 1. Vérification des points
        if ($user->points > $subTable->points_max) {
            return back()->with('error', 'Ton classement est trop élevé pour ce tableau.');
        }

        // 2. Vérification du créneau horaire (Déjà inscrit dans ce SuperTable ?)
        $alreadyRegisteredInSlot = $user->subTables()
            ->where('super_table_id', $subTable->super_table_id)
            ->exists();

        if ($alreadyRegisteredInSlot) {
            return back()->with('error', 'Tu es déjà inscrit à un tableau sur ce créneau horaire.');
        }

        // 3. Vérification de la capacité du SuperTable
        // On charge le superTable avec le compte des utilisateurs de tous ses sous-tableaux
        $superTable = $subTable->superTable()->with(['subTables.users'])->first();
        
        // On calcule le total des inscrits sur TOUS les sous-tableaux de ce créneau
        $currentInscriptions = $superTable->subTables->sum(function($sub) {
            return $sub->users->count();
        });
        
        if ($currentInscriptions >= $superTable->max_players) {
            return back()->with('error', 'Ce créneau horaire (Super Tableau) est complet.');
        }

        // 4. Inscription
        $user->subTables()->attach($subTable->id);
        
        return back()->with('success', 'Inscription validée !');
    }

    public function unregister(SubTable $subTable)
    {
        Auth::user()->subTables()->detach($subTable->id);
        return back()->with('success', 'Désinscription effectuée.');
    }

    public function redirectBasedOnRole()
    {
        $user = auth()->user();

        // Si l'utilisateur est un admin
        if ($user->is_admin) {
            return redirect()->route('admin.tournaments.index');
        }

        // Si l'utilisateur a un rôle ou une propriété "coach" 
        // (Ici on vérifie par exemple si son rôle est 'coach')
        if ($user->role === 'coach') { 
            return redirect()->route('coach.dashboard');
        }

        // Par défaut, c'est un joueur classique
        return $this->index(); 
    }
}