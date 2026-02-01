<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubTable;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoachController extends Controller
{
    /**
     * Affiche le dashboard du coach avec ses élèves et les tournois.
     */
    public function index()
    {
        // Utilisation du helper de rôle défini dans le Model User
        if (!auth()->user()->isCoach()) {
            return redirect()->route('dashboard')->with('error', "Accès réservé aux entraîneurs.");
        }

        // Récupération des élèves avec leurs inscriptions (Eager Loading pour la performance)
        $myPlayers = User::where('coach_id', auth()->id())
            ->with(['registrations.subTable.superTable.tournament'])
            ->get();
        
        // On récupère uniquement les tournois acceptés et publiés
        $availableSubTables = SubTable::whereHas('superTable.tournament', function($q) {
            $q->where('status', 'accepted')->where('is_published', true);
        })->with(['superTable.tournament', 'registrations'])->get(); 

        return view('coach.dashboard', compact('myPlayers', 'availableSubTables'));
    }

    /**
     * Création d'un compte élève lié au coach.
     */
    public function addStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'license_number' => 'required|string|unique:users,license_number',
            'points' => 'required|integer|min:500',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'license_number' => $validated['license_number'],
            'points' => $validated['points'],
            'password' => Hash::make($validated['password']),
            'role' => 'player',
            'coach_id' => auth()->id(),
        ]);

        return back()->with('success', "L'élève {$validated['name']} a été ajouté à votre groupe.");
    }

    /**
     * Inscrit un joueur (ou le coach lui-même) à un tableau.
     */
    public function registerPlayer(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'sub_table_id' => 'required|exists:sub_tables,id',
        ]);

        $subTable = SubTable::with('superTable.tournament')->findOrFail($request->sub_table_id);
        $player = User::findOrFail($request->player_id);
        $coach = auth()->user();

        // 1. SÉCURITÉ : Date limite de clôture
        if (now()->gt($subTable->superTable->tournament->registration_deadline)) {
            $dateFormatted = Carbon::parse($subTable->superTable->tournament->registration_deadline)->format('d/m/Y H:i');
            return back()->with('error', "Inscriptions impossibles : la date limite était le $dateFormatted.");
        }

        // 2. SÉCURITÉ : Propriété de l'élève
        if ($player->coach_id !== $coach->id && $player->id !== $coach->id) {
            return back()->with('error', "Vous n'avez pas l'autorisation pour ce joueur.");
        }

        // 3. VÉRIFICATION : Limite de 2 tableaux par TOURNOI
        $tournamentId = $subTable->superTable->tournament_id;
        $count = Registration::where('user_id', $player->id)
            ->whereHas('subTable.superTable', function($q) use ($tournamentId) {
                $q->where('tournament_id', $tournamentId);
            })->count();

        if ($count >= 2) {
            return back()->with('error', "{$player->name} est déjà inscrit à 2 tableaux dans ce tournoi.");
        }

        // 4. CONFLIT HORAIRE : Un seul tableau par bloc (SuperTable)
        $hasConflict = Registration::where('user_id', $player->id)
            ->whereHas('subTable', function($q) use ($subTable) {
                $q->where('super_table_id', $subTable->super_table_id);
            })->exists();

        if ($hasConflict) {
            return back()->with('error', "{$player->name} est déjà inscrit sur ce créneau horaire.");
        }

        // 5. NIVEAU : Vérification des points (snapshot à l'inscription)
        if ($player->points > $subTable->points_max || $player->points < $subTable->points_min) {
            return back()->with('error', "Le classement de {$player->name} ({$player->points} pts) ne correspond pas à ce tableau.");
        }

        // 6. CAPACITÉ : Gestion de la liste d'attente
        $status = $subTable->superTable->isFull() ? 'waiting_list' : 'confirmed';

        // 7. CRÉATION : Snapshot des données du joueur au moment de l'inscription
        Registration::create([
            'user_id' => $player->id,
            'sub_table_id' => $subTable->id,
            'created_by' => $coach->id,
            'status' => $status,
            'player_license' => $player->license_number,
            'player_points' => $player->points,
            'registered_at' => now(),
        ]);

        $msg = ($status === 'confirmed') ? "Inscription confirmée." : "Placé en liste d'attente.";
        return back()->with('success', "{$player->name} : $msg");
    }

    /**
     * Désinscrit un joueur.
     */
    public function unregisterPlayer(Request $request)
    {
        $registration = Registration::where('user_id', $request->player_id)
            ->where('sub_table_id', $request->sub_table_id)
            ->firstOrFail();
            
        // Seul le créateur (coach), le joueur lui-même ou le super_admin peut désinscrire
        if ($registration->created_by !== auth()->id() && 
            $registration->user_id !== auth()->id() && 
            !auth()->user()->isSuperAdmin()) {
            abort(403, "Action non autorisée.");
        }

        $registration->delete();

        return back()->with('success', "Désinscription effectuée.");
    }
}