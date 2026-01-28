<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubTable;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CoachController extends Controller
{
    public function index()
    {
        // Utilisation du helper de rôle qu'on a créé dans le Model User
        if (!auth()->user()->isCoach()) {
            return redirect()->route('dashboard')->with('error', "Accès réservé aux entraîneurs.");
        }

        $myPlayers = User::where('coach_id', auth()->id())->get();
        
        // On récupère uniquement les tournois acceptés et publiés
        $availableSubTables = SubTable::whereHas('superTable.tournament', function($q) {
            $q->where('status', 'accepted')->where('is_published', true);
        })->with(['superTable.tournament', 'registrations'])->get(); 

        return view('coach.dashboard', compact('myPlayers', 'availableSubTables'));
    }

    public function registerPlayer(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'sub_table_id' => 'required|exists:sub_tables,id',
        ]);

        $subTable = SubTable::with('superTable')->findOrFail($request->sub_table_id);
        $player = User::findOrFail($request->player_id);
        $coach = auth()->user();

        // 1. Sécurité : Propriété de l'élève
        if ($player->coach_id !== $coach->id && $player->id !== $coach->id) {
            return back()->with('error', "Vous n'avez pas l'autorisation pour ce joueur.");
        }

        // 2. Vérification des conflits (Un seul tableau par bloc horaire)
        $hasConflict = Registration::where('user_id', $player->id)
            ->whereHas('subTable', function($q) use ($subTable) {
                $q->where('super_table_id', $subTable->super_table_id);
            })->exists();

        if ($hasConflict) {
            return back()->with('error', "{$player->name} est déjà inscrit sur ce créneau horaire.");
        }

        // 3. Limite de niveau (Points)
        if ($player->points > $subTable->points_max || $player->points < $subTable->points_min) {
            return back()->with('error', "Le classement de {$player->name} ne correspond pas à ce tableau.");
        }

        // 4. Gestion de la capacité et Liste d'attente
        $status = $subTable->superTable->isFull() ? 'waiting_list' : 'confirmed';

        // 5. Création de l'inscription (Le modèle Registration gère le snapshot automatiquement)
        Registration::create([
            'user_id' => $player->id,
            'sub_table_id' => $subTable->id,
            'created_by' => $coach->id,
            'status' => $status,
            'player_license' => $player->license_number,
            'player_firstname' => $player->first_name, // Assure-toi que ces champs existent
            'player_lastname' => $player->last_name,
            'player_points' => $player->points,
        ]);

        $msg = ($status === 'confirmed') ? "Inscription confirmée." : "Placé en liste d'attente.";
        return back()->with('success', "{$player->name} : $msg");
    }

    public function unregisterPlayer(Request $request)
    {
        $registration = Registration::where('user_id', $request->player_id)
            ->where('sub_table_id', $request->sub_table_id)
            ->firstOrFail();
            
        // Seul le créateur (coach) ou le super_admin peut désinscrire
        if ($registration->created_by !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $registration->delete();

        return back()->with('success', "Désinscription effectuée.");
    }
}