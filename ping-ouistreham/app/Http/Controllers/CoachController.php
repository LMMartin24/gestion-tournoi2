<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoachController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'coach') {
            return redirect()->route('dashboard')->with('error', "Accès réservé aux entraîneurs.");
        }
        $coach = Auth::user();
        $myPlayers = User::where('coach_id', $coach->id)->get();
        // On charge les relations pour calculer le remplissage global du créneau
        $availableSubTables = SubTable::with(['superTable.subTables.users', 'users'])->get(); 

        return view('coach.dashboard', compact('myPlayers', 'availableSubTables'));
    }

    public function addStudent(Request $request)
    {
        $request->validate([
            'license_number' => 'required|string|unique:users,license_number',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8',
            'name'           => 'required|string|max:255',
        ]);

        $coach = auth()->user();

        $student = User::create([
            'name'           => $request->name,
            'license_number' => $request->license_number,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'coach_id'       => $coach->id,
            'points'         => 500,
        ]);

        return back()->with('success', "Le compte de {$student->name} a été créé.");
    }

    public function registerPlayer(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'sub_table_id' => 'required|exists:sub_tables,id',
        ]);

        $subTable = SubTable::with(['superTable.subTables.users'])->findOrFail($request->sub_table_id);
        $player = User::with('subTables')->findOrFail($request->player_id);
        $coach = auth()->user();

        // 1. Sécurité : Propriété de l'élève
        if ($player->coach_id !== $coach->id && $player->id !== $coach->id) {
            return back()->with('error', "Vous n'avez pas l'autorisation d'inscrire ce joueur.");
        }

        // --- NOUVELLE SÉCURITÉ : LIMITE DE 2 TABLEAUX ---
        if ($player->subTables->count() >= 2) {
            return back()->with('error', "{$player->name} a déjà atteint la limite maximale de 2 tableaux.");
        }
        // ------------------------------------------------

        // 2. Vérification des points
        if ($player->points > $subTable->points_max) {
            return back()->with('error', "{$player->name} a trop de points pour ce tableau.");
        }

        // 3. Vérification du SuperTableau (Capacité globale)
        $superTable = $subTable->superTable;
        $totalInscribedInSlot = $superTable->subTables->sum(fn($sub) => $sub->users->count());

        if ($totalInscribedInSlot >= $superTable->max_players) {
            return back()->with('error', "Le créneau horaire est complet.");
        }

        // 4. Inscription
        $player->subTables()->syncWithoutDetaching([$subTable->id]);

        return back()->with('success', "{$player->name} est inscrit au {$subTable->label}.");
    }

    public function unregisterPlayer(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'sub_table_id' => 'required|exists:sub_tables,id',
        ]);

        $player = User::findOrFail($request->player_id);
        
        // On détache le joueur du tableau spécifique
        $player->subTables()->detach($request->sub_table_id);

        return back()->with('success', "{$player->name} a été désinscrit avec succès.");
    }
}