<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubTable;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Utilisation de tes nouveaux Mailables avec suffixe Coach
use App\Mail\RegistrationConfirmationCoach;
use App\Mail\UnregistrationNotificationCoach;

class CoachController extends Controller
{
    /**
     * Affiche le dashboard du coach avec ses élèves et les tournois.
     */
    public function index()
    {
        if (!auth()->user()->isCoach()) {
            return redirect()->route('dashboard')->with('error', "Accès réservé aux entraîneurs.");
        }

        // Récupération des élèves avec leurs inscriptions
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
        // 1. Validation avec ajout du champ 'club'
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:users,license_number',
            'points' => 'required|integer|min:500',
            'club' => 'nullable|string|max:255',
        ]);

        // 2. Génération de l'email automatique
        $cleanName = str_replace('-', '', Str::slug($validated['name']));
        $generatedEmail = $cleanName . '@tennisdetabledeouistreham.com';

        $finalEmail = $generatedEmail;
        $count = 1;
        while (User::where('email', $finalEmail)->exists()) {
            $finalEmail = $cleanName . $count . '@tennisdetabledeouistreham.com';
            $count++;
        }

        // 3. Mot de passe aléatoire
        $randomPassword = Str::random(10);

        // 4. Création de l'élève
        $studentClub = $validated['club'] ?? auth()->user()->club;

        $student = User::create([
            'name' => $validated['name'],
            'email' => $finalEmail,
            'license_number' => $validated['license_number'],
            'points' => $validated['points'],
            'password' => Hash::make($randomPassword),
            'password_plain' => $randomPassword,
            'role' => 'player',
            'coach_id' => auth()->id(),
            'club' => $studentClub,
        ]);

        return back()->with('success', "L'élève {$student->name} a été ajouté au club {$studentClub}.");
    }

    /**
     * Inscrit un joueur (ou le coach) à un tableau.
     */
public function registerPlayer(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'sub_table_id' => 'required|exists:sub_tables,id',
        ]);

        // On charge bien toute la hiérarchie pour le mail
        $subTable = SubTable::with(['superTable.tournament'])->findOrFail($request->sub_table_id);
        $superTable = $subTable->superTable;
        $player = User::findOrFail($request->player_id);
        $coach = auth()->user();

        // 1. SÉCURITÉ : Date limite
        if (now()->gt($superTable->tournament->registration_deadline)) {
            $dateFormatted = Carbon::parse($superTable->tournament->registration_deadline)->format('d/m/Y H:i');
            return back()->with('error', "Inscriptions closes depuis le $dateFormatted.");
        }

        // 2. SÉCURITÉ : Droits (Coach sur élève OU Coach sur lui-même)
        if ($player->coach_id != $coach->id && $player->id != $coach->id) {
            return back()->with('error', "Action non autorisée pour ce joueur.");
        }

        // 3. LIMITE : 2 tableaux
        $tournamentId = $superTable->tournament_id;
        $count = Registration::where('user_id', $player->id)
            ->whereHas('subTable.superTable', function($q) use ($tournamentId) {
                $q->where('tournament_id', $tournamentId);
            })->count();

        if ($count >= 2) {
            return back()->with('error', "{$player->name} a déjà atteint la limite de 2 tableaux.");
        }

        // 4. CONFLIT HORAIRE
        $hasConflict = Registration::where('user_id', $player->id)
            ->whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })->exists();

        if ($hasConflict) {
            return back()->with('error', "{$player->name} est déjà inscrit sur ce créneau horaire.");
        }

        // 5. NIVEAU
        if ($player->points > $subTable->points_max || $player->points < $subTable->points_min) {
            return back()->with('error', "Le classement ({$player->points} pts) ne permet pas l'accès à ce tableau.");
        }

        // 6. CAPACITÉ
        $currentInscriptionsCount = Registration::whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })
            ->where('status', 'confirmed')
            ->count();

        $limit = (int) $superTable->max_players;
        $status = ($currentInscriptionsCount < $limit) ? 'confirmed' : 'waiting_list';

        // 7. PRÉPARATION NOM/PRÉNOM
        $nameParts = explode(' ', trim($player->name));
        $firstname = $nameParts[0];
        $lastname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        // 8. CRÉATION
        try {
            $registration = Registration::create([
                'user_id' => $player->id,
                'sub_table_id' => $subTable->id,
                'created_by' => $coach->id,
                'status' => $status,
                'player_license' => $player->license_number,
                'player_points' => $player->points,
                'player_firstname' => $firstname,
                'player_lastname' => $lastname,
                'registered_at' => now(),
            ]);

            // Important : On recharge les relations sur l'objet créé pour le mail
            $registration->load(['subTable.superTable.tournament', 'user']);

            // 9. ENVOI DU MAIL (On passe le coach en 2ème argument)
            Mail::to('tournoi-apo@skopee.fr')->send(new RegistrationConfirmationCoach($registration, $coach));

            $msg = ($status === 'confirmed') ? "inscrit !" : "en LISTE D'ATTENTE.";
            return back()->with('success', "{$player->name} est {$msg} L'admin a été prévenu.");

        } catch (\Exception $e) {
            Log::error("Erreur inscription coach : " . $e->getMessage());
            return back()->with('error', "Erreur technique.");
        }
    }
    /**
     * Désinscrit un joueur et prévient l'admin.
     */
    public function unregisterPlayer(Request $request)
    {
        // On récupère les relations nécessaires pour le mail
        $registration = Registration::with(['subTable.superTable.tournament', 'user'])
            ->where('user_id', $request->player_id)
            ->where('sub_table_id', $request->sub_table_id)
            ->firstOrFail();
            
        $user = auth()->user();
        $player = $registration->user;
            
        // Sécurité : On autorise si c'est le coach du joueur OU le créateur OU l'admin
        if ($player->coach_id === $user->id || 
            $registration->created_by === $user->id || 
            $user->isSuperAdmin()) 
        {
            try {
                // On passe l'utilisateur connecté (le coach) au Mailable
                Mail::to('tournoi-apo@skopee.fr')->send(new UnregistrationNotificationCoach($registration, $user));
            } catch (\Exception $e) {
                Log::error("Erreur mail désinscription : " . $e->getMessage());
            }

            $registration->delete();
            return back()->with('success', "Désinscription effectuée et admin prévenu.");
        }

        abort(403, "Action non autorisée.");
    }
}