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

        // Récupération des élèves avec leurs inscriptions (parfait)
        $myPlayers = User::where('coach_id', auth()->id())
            ->with(['registrations.subTable.superTable.tournament'])
            ->get();
        
        // Optimisation : on charge registrations SUR la superTable
        $availableSubTables = SubTable::whereHas('superTable.tournament', function($q) {
            $q->where('status', 'accepted')->where('is_published', true);
        })->with([
            'superTable.tournament', 
            'superTable.registrations', // Crucial pour le calcul du remplissage (%)
            'registrations'             // Crucial pour savoir qui de mon équipe est déjà inscrit
        ])->get(); 

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
/**
     * Inscrit un joueur (ou le coach) à un tableau.
     * Refuse l'inscription si la série (SuperTable) est complète.
     */
    public function registerPlayer(Request $request)
    {
        $request->validate([
            'player_id' => 'required|exists:users,id',
            'sub_table_id' => 'required|exists:sub_tables,id',
        ]);

        // 1. Chargement des données et relations
        $subTable = SubTable::with(['superTable.tournament'])->findOrFail($request->sub_table_id);
        $superTable = $subTable->superTable;
        $player = User::findOrFail($request->player_id);
        $coach = auth()->user();

        // 2. SÉCURITÉ : Droits du coach
        if ($player->coach_id !== $coach->id && $player->id !== $coach->id) {
            return back()->with('error', "Action non autorisée pour ce joueur.");
        }

        // 3. VÉRIFICATION DE LA CAPACITÉ (SUPER TABLE)
        // On compte toutes les inscriptions confirmées pour cette série
        $currentInscriptionsCount = Registration::whereHas('subTable', function($q) use ($superTable) {
            $q->where('super_table_id', $superTable->id);
        })
        ->where('status', 'confirmed')
        ->count();

        $limit = (int) $superTable->max_players;

        // Bloquer si la limite est atteinte
        if ($currentInscriptionsCount >= $limit) {
            return back()->with('error', "Impossible d'inscrire {$player->name} : Le tableau {$superTable->name} est complet ({$limit}/{$limit}).");
        }

        // 4. VÉRIFICATION DES POINTS
        if ($player->points > $subTable->points_max || $player->points < $subTable->points_min) {
            return back()->with('error', "Le classement ({$player->points} pts) ne permet pas l'accès à ce tableau.");
        }

        // 5. PRÉPARATION DU NOM
        $nameParts = explode(' ', trim($player->name));
        $firstname = $nameParts[0];
        $lastname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        // 6. CRÉATION ET ENVOI MAIL
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

            // Envoi du mail avec l'objet registration ET l'objet coach (pour le nom dans le mail)
            Mail::to('tournoi-apo@skopee.fr')->send(new RegistrationConfirmationCoach($registration, $coach));

            return back()->with('success', "{$player->name} est inscrit avec succès !");

        } catch (\Exception $e) {
            Log::error("Erreur inscription coach : " . $e->getMessage());
            return back()->with('error', "Erreur technique lors de l'inscription.");
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