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

// Mailables avec suffixe Coach
use App\Mail\RegistrationConfirmationCoach;
use App\Mail\UnregistrationNotificationCoach;

class CoachController extends Controller
{
    /**
     * Dashboard du coach.
     */
    public function index()
    {
        if (!auth()->user()->isCoach()) {
            return redirect()->route('dashboard')->with('error', "Accès réservé aux entraîneurs.");
        }

        $myPlayers = User::where('coach_id', auth()->id())
            ->with(['registrations.subTable.superTable.tournament'])
            ->get();
        
        $availableSubTables = SubTable::whereHas('superTable.tournament', function($q) {
            $q->where('status', 'accepted')->where('is_published', true);
        })->with([
            'superTable.tournament', 
            'superTable.registrations',
            'registrations'
        ])->get(); 

        return view('coach.dashboard', compact('myPlayers', 'availableSubTables'));
    }

    /**
     * Création d'un compte élève.
     */
    public function addStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:users,license_number',
            'points' => 'required|integer|min:500',
            'club' => 'nullable|string|max:255',
        ]);

        $cleanName = str_replace('-', '', Str::slug($validated['name']));
        $generatedEmail = $cleanName . '@tennisdetabledeouistreham.com';

        $finalEmail = $generatedEmail;
        $count = 1;
        while (User::where('email', $finalEmail)->exists()) {
            $finalEmail = $cleanName . $count . '@tennisdetabledeouistreham.com';
            $count++;
        }

        $randomPassword = Str::random(10);
        $studentClub = $validated['club'] ?? auth()->user()->club;

        $student = User::create([
            'name' => strtoupper($validated['name']),
            'email' => $finalEmail,
            'license_number' => $validated['license_number'],
            'points' => $validated['points'],
            'password' => Hash::make($randomPassword),
            'password_plain' => $randomPassword,
            'role' => 'player',
            'coach_id' => auth()->id(),
            'club' => $studentClub,
        ]);

        return back()->with('success', "L'élève {$student->name} a été ajouté.");
    }

    /**
     * Inscription : Pas de liste d'attente. Bloqué si complet ou VERROUILLÉ.
     */
    public function registerPlayer(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'sub_table_id' => 'required|exists:sub_tables,id',
        ]);

        $subTable = SubTable::with(['superTable.registrations'])->findOrFail($request->sub_table_id);
        $superTable = $subTable->superTable;
        $player = User::findOrFail($request->player_id);
        $coach = auth()->user();

        // 1. Autorisation
        if ($player->coach_id !== $coach->id && $player->id !== $coach->id) {
            return back()->with('error', "Action non autorisée.");
        }

        // 2. Vérification Verrouillage (NOUVEAU)
        if ($superTable->is_locked) {
            return back()->with('error', "La série {$superTable->label} est verrouillée par l'organisation. Inscriptions impossibles.");
        }

        // 2 bis. Vérification Capacité (Strictement bloqué si plein)
        $currentInscriptionsCount = $superTable->registrations->where('status', 'confirmed')->count();
        $limit = (int) $superTable->max_players;

        if ($currentInscriptionsCount >= $limit) {
            return back()->with('error', "Ce tableau est complet. Inscription impossible.");
        }

        // 3. Vérification des points
        if ($player->points > $subTable->points_max || $player->points < $subTable->points_min) {
            return back()->with('error', "Le classement ({$player->points} pts) est hors limites pour ce tableau.");
        }

        // 4. Limite de 2 tableaux par tournoi
        $existingCount = Registration::where('user_id', $player->id)
            ->whereHas('subTable.superTable', function($q) use ($superTable) {
                $q->where('tournament_id', $superTable->tournament_id);
            })->count();

        if ($existingCount >= 2) {
            return back()->with('error', "Le joueur est déjà inscrit à 2 tableaux.");
        }

        // 5. Extraction Nom/Prénom
        $nameParts = explode(' ', trim($player->name));
        $firstname = $nameParts[0];
        $lastname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        try {
            $registration = Registration::create([
                'user_id' => $player->id,
                'sub_table_id' => $subTable->id,
                'created_by' => $coach->id,
                'status' => 'confirmed', 
                'player_license' => $player->license_number,
                'player_points' => $player->points,
                'player_firstname' => $firstname,
                'player_lastname' => $lastname,
                'registered_at' => now(),
            ]);

            Mail::to('tournoi-apo@skopee.fr')->send(new RegistrationConfirmationCoach($registration, $coach));

            return back()->with('success', "{$player->name} est inscrit avec succès !");

        } catch (\Exception $e) {
            Log::error("Erreur inscription : " . $e->getMessage());
            return back()->with('error', "Erreur technique.");
        }
    }

    /**
     * Désinscription (La désinscription reste possible même si le tableau est locked).
     */
    public function unregisterPlayer(Request $request)
    {
        $registration = Registration::with(['subTable.superTable.tournament', 'user'])
            ->where('user_id', $request->player_id)
            ->where('sub_table_id', $request->sub_table_id)
            ->firstOrFail();
            
        $user = auth()->user();
        $player = $registration->user;
            
        if ($player->coach_id === $user->id || $registration->created_by === $user->id || $user->isAdmin()) 
        {
            try {
                Mail::to('tournoi-apo@skopee.fr')->send(new UnregistrationNotificationCoach($registration, $user));
            } catch (\Exception $e) {
                Log::error("Erreur mail désinscription : " . $e->getMessage());
            }

            $registration->delete();
            return back()->with('success', "Le joueur a été désinscrit.");
        }

        abort(403, "Action non autorisée.");
    }
}