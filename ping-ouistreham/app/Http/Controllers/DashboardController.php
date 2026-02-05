<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubTable;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use App\Mail\RegistrationConfirmation;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        // 2. Tableaux disponibles (Visibilité + Points)
        $availableSubTables = SubTable::whereHas('superTable.tournament', function($q) {
                $q->where('status', 'accepted')->where('is_published', true);
            })
            ->where('points_max', '>=', $user->points)
            ->where('points_min', '<=', $user->points)
            ->whereDoesntHave('registrations', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['superTable.tournament', 'superTable.registrations'])
            ->get();

        // 3. Calcul du montant total
        $totalToPay = $myRegistrations->where('status', 'confirmed')->sum(function($reg) {
            return $reg->subTable->entry_fee;
        });

        return view('dashboard', compact('myRegistrations', 'availableSubTables', 'totalToPay'));
    }

    /**
     * Inscription classique (bloquée si plein ou VERROUILLÉ).
     */
    public function register(Request $request, SubTable $subTable)
    {
        $user = Auth::user();
        $superTable = $subTable->superTable;
        $tournament = $superTable->tournament;

        // 1. SÉCURITÉ : Date limite de clôture
        if (now()->gt($tournament->registration_deadline)) {
            $dateFormatted = Carbon::parse($tournament->registration_deadline)->format('d/m/Y H:i');
            return back()->with('error', "Trop tard ! Les inscriptions sont fermées depuis le $dateFormatted.");
        }

        // 2. SÉCURITÉ : Vérification du statut verrouillé (NOUVEAU)
        if ($superTable->is_locked) {
            return back()->with('error', "Les inscriptions pour la série {$superTable->label} sont actuellement verrouillées par l'organisateur.");
        }

        // 3. LIMITE DE 2 TABLEAUX PAR TOURNOI
        $registrationsInThisTournament = Registration::where('user_id', $user->id)
            ->whereHas('subTable.superTable', function($q) use ($tournament) {
                $q->where('tournament_id', $tournament->id);
            })->count();

        if ($registrationsInThisTournament >= 2) {
            return back()->with('error', 'Tu es déjà inscrit à 2 tableaux pour ce tournoi.');
        }

        // 4. VÉRIFICATION DES POINTS
        if ($user->points > $subTable->points_max || $user->points < $subTable->points_min) {
            return back()->with('error', 'Ton classement (' . $user->points . ' pts) ne correspond pas à ce tableau.');
        }

        // 5. VÉRIFICATION DU CRÉNEAU (Une seule inscription par SuperTable)
        $alreadyInSlot = Registration::where('user_id', $user->id)
            ->whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })->exists();

        if ($alreadyInSlot) {
            return back()->with('error', 'Tu es déjà inscrit dans la série ' . $superTable->label . '.');
        }

        // 6. CAPACITÉ : Blocage si plein
        $totalConfirmed = Registration::whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })
            ->where('status', 'confirmed')
            ->count();

        $limit = (int) $superTable->max_players;

        if ($totalConfirmed >= $limit) {
            return back()->with('error', "Ce tableau est complet. L'organisation gère désormais la liste d'attente manuellement.");
        }

        // 7. Découpage du nom
        $nameParts = explode(' ', trim($user->name));
        $firstname = $nameParts[0];
        $lastname = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        // 8. CRÉATION DE L'INSCRIPTION
        try {
            $registration = Registration::create([
                'user_id'          => $user->id,
                'created_by'       => $user->id,
                'sub_table_id'     => $subTable->id,
                'player_license'   => $user->license_number,
                'player_firstname' => $firstname,
                'player_lastname'  => $lastname,
                'player_points'    => $user->points,
                'status'           => 'confirmed',
                'priority'         => 'primary',
                'registered_at'    => now(),
            ]);

            Mail::to('tournoi-apo@skopee.fr')->send(new RegistrationConfirmation($registration));

            return back()->with('success', 'Inscription validée pour le ' . $subTable->label . ' !');

        } catch (\Exception $e) {
            Log::error("Erreur inscription classique : " . $e->getMessage());
            return back()->with('error', 'Une erreur technique est survenue lors de l\'inscription.');
        }
    }

    /**
     * Désinscription (La désinscription reste autorisée même si locked).
     */
    public function unregister(SubTable $subTable)
    {
        $registration = Registration::where('user_id', auth()->id())
            ->where('sub_table_id', $subTable->id)
            ->first();

        if (!$registration) {
            return back()->with('error', "Inscription introuvable.");
        }

        try {
            Mail::to('tournoi-apo@skopee.fr')->send(new \App\Mail\UnregistrationNotification($registration));
            
            $registration->delete();
            return back()->with('success', 'Ta désinscription a bien été prise en compte.');
            
        } catch (\Exception $e) {
            Log::error("Erreur désinscription : " . $e->getMessage());
            $registration->delete(); 
            return back()->with('success', 'Désinscription effectuée (erreur mail notification).');
        }
    }

    public function redirectBasedOnRole()
    {
        $user = auth()->user();

        if ($user->isAdmin() || (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
            return redirect()->route('admin.tournaments.index');
        }

        if (method_exists($user, 'isCoach') && $user->isCoach()) {
            return redirect()->route('coach.dashboard');
        }

        return redirect()->route('player.dashboard');
    }
}