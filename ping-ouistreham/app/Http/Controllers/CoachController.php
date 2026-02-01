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
use Illuminate\Support\Str;
use App\Mail\WelcomeStudentMail;
use Illuminate\Support\Facades\Mail;

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
        // 1. Validation des données entrantes
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:users,license_number',
            'points' => 'required|integer|min:500',
        ]);

        // 2. Génération de l'email : nomprenom@tennisdetabledeouistreham.com
        // On nettoie le nom (slug) et on enlève les tirets
        $cleanName = str_replace('-', '', Str::slug($validated['name']));
        $generatedEmail = $cleanName . '@tennisdetabledeouistreham.com';

        // Sécurité : Si l'email existe déjà (homonyme), on ajoute un chiffre
        $finalEmail = $generatedEmail;
        $count = 1;
        while (User::where('email', $finalEmail)->exists()) {
            $finalEmail = $cleanName . $count . '@tennisdetabledeouistreham.com';
            $count++;
        }

        // 3. Génération du mot de passe aléatoire
        $randomPassword = Str::random(10);

        // 4. Création de l'utilisateur en base de données
        $student = User::create([
            'name' => $validated['name'],
            'email' => $finalEmail,
            'license_number' => $validated['license_number'],
            'points' => $validated['points'],
            'password' => Hash::make($randomPassword), // Version cryptée pour la sécurité Laravel
            'password_plain' => $randomPassword,       // Version CLAIRE pour l'affichage coach
            'role' => 'player',
            'coach_id' => auth()->id(),
            'club' => auth()->user()->club,            // On lui donne le même club que le coach
        ]);

        // 5. Envoi de l'email de bienvenue (Optionnel, nécessite config .env)
        try {
            Mail::to($student->email)->send(new WelcomeStudentMail($student, $randomPassword));
        } catch (\Exception $e) {
            // On continue même si l'email ne part pas (utile en local sans config mail)
        }

        return back()->with('success', "L'élève {$student->name} a été ajouté. Email : {$student->email}");
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
        $superTable = $subTable->superTable;
        $player = User::findOrFail($request->player_id);
        $coach = auth()->user();

        // 1. SÉCURITÉ : Date limite de clôture
        if (now()->gt($subTable->superTable->tournament->registration_deadline)) {
            $dateFormatted = \Carbon\Carbon::parse($subTable->superTable->tournament->registration_deadline)->format('d/m/Y H:i');
            return back()->with('error', "Inscriptions impossibles : la date limite était le $dateFormatted.");
        }

        // 2. SÉCURITÉ : Propriété de l'élève (Coach ou lui-même)
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
            ->whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })->exists();

        if ($hasConflict) {
            return back()->with('error', "{$player->name} est déjà inscrit sur ce créneau horaire.");
        }

        // 5. NIVEAU : Vérification des points
        if ($player->points > $subTable->points_max || $player->points < $subTable->points_min) {
            return back()->with('error', "Le classement de {$player->name} ({$player->points} pts) ne correspond pas à ce tableau.");
        }

        // 6. CAPACITÉ : Vérification du nombre d'inscrits CONFIRMÉS sur le SuperTable
        $currentInscriptionsCount = Registration::whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })
            ->where('status', 'confirmed')
            ->count();

        // On détermine le statut selon la limite du SuperTable
        $status = ($currentInscriptionsCount < $superTable->max_players) ? 'confirmed' : 'waiting_list';

        // 7. PRÉPARATION DES DONNÉES (Nom/Prénom pour l'export)
        $nameParts = explode(' ', trim($player->name));
        $firstname = $nameParts[0];
        $lastname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        // 8. CRÉATION
        Registration::create([
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

        if ($status === 'confirmed') {
            return back()->with('success', "{$player->name} est inscrit avec succès !");
        } else {
            return back()->with('error', "Attention : {$player->name} a été placé en LISTE D'ATTENTE (Tableau complet).");
        }
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