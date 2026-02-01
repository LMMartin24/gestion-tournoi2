<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubTable;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du joueur.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Inscriptions actuelles du joueur
        $myRegistrations = Registration::where('user_id', $user->id)
            ->with(['subTable.superTable.tournament'])
            ->get();

        // 2. Tableaux disponibles (Filtrage par points et visibilité)
        // Utilisation du nom $availableSubTables pour correspondre à ton erreur précédente
        $availableSubTables = SubTable::whereHas('superTable.tournament', function($q) {
                $q->where('status', 'accepted')->where('is_published', true);
            })
            ->where('points_max', '>=', $user->points)
            ->where('points_min', '<=', $user->points)
            ->whereDoesntHave('registrations', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['superTable.tournament'])
            ->get();

        // 3. Calcul du montant total à régler
        $totalToPay = $myRegistrations->where('status', 'confirmed')->sum(function($reg) {
            return $reg->subTable->entry_fee;
        });

        return view('dashboard', compact('myRegistrations', 'availableSubTables', 'totalToPay'));
    }

    /**
     * Gère l'inscription d'un joueur individuel (Dashboard classique).
     */
    public function register(Request $request, SubTable $subTable)
    {
        $user = Auth::user();
        $superTable = $subTable->superTable;
        $tournament = $superTable->tournament;

        // 1. SÉCURITÉ : Date limite de clôture
        if (now()->gt($tournament->registration_deadline)) {
            $dateFormatted = \Carbon\Carbon::parse($tournament->registration_deadline)->format('d/m/Y H:i');
            return back()->with('error', "Trop tard ! Les inscriptions sont fermées depuis le $dateFormatted.");
        }

        // 2. LIMITE DE 2 TABLEAUX PAR TOURNOI
        $registrationsInThisTournament = Registration::where('user_id', $user->id)
            ->whereHas('subTable.superTable', function($q) use ($tournament) {
                $q->where('tournament_id', $tournament->id);
            })->count();

        if ($registrationsInThisTournament >= 2) {
            return back()->with('error', 'Limite atteinte : Tu es déjà inscrit à 2 tableaux pour ce tournoi.');
        }

        // 3. VÉRIFICATION DES POINTS
        if ($user->points > $subTable->points_max || $user->points < $subTable->points_min) {
            return back()->with('error', 'Ton classement (' . $user->points . ' pts) ne correspond pas à ce tableau.');
        }

        // 4. VÉRIFICATION DU CRÉNEAU (Une seule inscription par SuperTable)
        $alreadyInSlot = Registration::where('user_id', $user->id)
            ->whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })->exists();

        if ($alreadyInSlot) {
            return back()->with('error', 'Tu es déjà inscrit dans la série ' . $superTable->name . ' (créneau identique).');
        }

        // 5. CAPACITÉ : Vérification du nombre d'inscrits CONFIRMÉS sur le SuperTable
        $totalConfirmed = Registration::whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })
            ->where('status', 'confirmed')
            ->count();

        // CORRECTIF : Forçage du type entier et comparaison stricte
        $limit = (int) $superTable->max_players;
        $status = ($totalConfirmed < $limit) ? 'confirmed' : 'waiting_list';

        // 6. DÉCOUPAGE DU NOM (Pour l'export LibreOffice)
        $nameParts = explode(' ', trim($user->name));
        $firstname = $nameParts[0];
        $lastname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        // 7. CRÉATION DE L'INSCRIPTION
        try {
            Registration::create([
                'user_id' => $user->id,
                'created_by' => $user->id,
                'sub_table_id' => $subTable->id,
                'player_license' => $user->license_number,
                'player_firstname' => $firstname,
                'player_lastname' => $lastname,
                'player_points' => $user->points,
                'status' => $status,
                'priority' => 'primary',
                'registered_at' => now(),
            ]);

            if ($status === 'confirmed') {
                return back()->with('success', 'Inscription validée pour le ' . $subTable->label . ' !');
            } else {
                // Changement en 'error' ou 'warning' pour bien marquer la liste d'attente visuellement
                return back()->with('error', 'Série complète : tu as été placé en LISTE D\'ATTENTE pour le ' . $subTable->label . '.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'inscription.');
        }
    }
    /**
     * Annule une inscription (Dashboard classique).
     */
    public function unregister(SubTable $subTable)
    {
        // 1. On récupère l'instance précise AVEC ses relations
        $registration = Registration::with('subTable.superTable')
            ->where('user_id', auth()->id())
            ->where('sub_table_id', $subTable->id)
            ->first();

        // 2. On vérifie si l'inscription existe avant de tenter la suppression
        if (!$registration) {
            return back()->with('error', "Inscription introuvable.");
        }

        // 3. On appelle delete() sur l'objet lui-même. 
        // C'est cette action précise qui déclenche l'Observer !
        $registration->delete();

        return back()->with('success', 'Désinscription effectuée. Si quelqu\'un attendait, il a pris ta place !');
    }

    /**
     * Redirige l'utilisateur vers son dashboard spécifique selon son rôle.
     */
    public function redirectBasedOnRole()
    {
        $user = auth()->user();

        if ($user->isAdmin() || (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
            return redirect()->route('admin.tournaments.index');
        }

        if (method_exists($user, 'isCoach') && $user->isCoach()) {
            return redirect()->route('coach.dashboard');
        }

        // CORRECT : On redirige vers la route nommée qui pointe vers index()
        return redirect()->route('player.dashboard');
    }
    
}