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
     * Gère l'inscription d'un joueur à un tableau avec une limite de 2.
     */
    public function register(Request $request, SubTable $subTable)
    {
        $user = Auth::user();
        $tournament = $subTable->superTable->tournament;

        // 1. LIMITE DE 2 TABLEAUX PAR TOURNOI
        // On compte combien d'inscriptions l'utilisateur possède déjà pour ce tournoi précis
        $registrationsInThisTournament = Registration::where('user_id', $user->id)
            ->whereHas('subTable.superTable', function($q) use ($tournament) {
                $q->where('tournament_id', $tournament->id);
            })->count();

        if ($registrationsInThisTournament >= 2) {
            return back()->with('error', 'Limite atteinte : Tu ne peux pas t\'inscrire à plus de 2 tableaux pour ce tournoi.');
        }

        // 2. VÉRIFICATION DES POINTS
        if ($user->points > $subTable->points_max || $user->points < $subTable->points_min) {
            return back()->with('error', 'Ton classement (' . $user->points . ' pts) ne correspond pas à ce tableau.');
        }

        // 3. VÉRIFICATION DU CRÉNEAU (Une seule inscription par SuperTable/Série)
        $alreadyInSlot = Registration::where('user_id', $user->id)
            ->whereHas('subTable', function($q) use ($subTable) {
                $q->where('super_table_id', $subTable->super_table_id);
            })->exists();

        if ($alreadyInSlot) {
            return back()->with('error', 'Tu as déjà un tableau prévu dans la série ' . $subTable->superTable->name . '.');
        }

        // 4. DÉTERMINATION DU STATUT (Gestion de la liste d'attente)
        // On vérifie si la SuperTable est pleine via ta méthode isFull()
        $status = $subTable->superTable->isFull() ? 'waiting_list' : 'confirmed';

        // 5. CRÉATION DE L'INSCRIPTION
        try {
            $registration = new Registration();
            
            // Données d'identification
            $registration->user_id = $request->user_id ?? Auth::id();
            $registration->created_by = Auth::id();
            $registration->sub_table_id = $subTable->id;

            // Informations du joueur au moment de l'inscription (historisation)
            $registration->player_license = $user->license_number;
            $registration->player_firstname = $user->name; // ou sépare si tu as first/last name
            $registration->player_lastname = $user->last_name ?? ''; 
            $registration->player_points = $user->points;

            $registration->status = $status;
            $registration->priority = 'primary';

            $registration->save();

            $message = ($status === 'confirmed') 
                ? 'Inscription validée pour le ' . $subTable->label . ' !' 
                : 'Série complète : tu as été placé en liste d\'attente pour le ' . $subTable->label . '.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'inscription.');
        }
    }

    /**
     * Annule une inscription.
     */
    public function unregister(SubTable $subTable)
    {
        Registration::where('user_id', auth()->id())
            ->where('sub_table_id', $subTable->id)
            ->delete();

        return back()->with('success', 'Désinscription effectuée.');
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