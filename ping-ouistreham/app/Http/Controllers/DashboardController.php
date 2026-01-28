<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubTable;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. On récupère les inscriptions du joueur via le modèle Registration
        // Cela permet de voir le statut (confirmé / liste d'attente)
        $myRegistrations = Registration::where('user_id', $user->id)
            ->with(['subTable.superTable.tournament'])
            ->get();

        // 2. Tableaux disponibles : Filtrage par points et visibilité
        $availableSubTables = SubTable::whereHas('superTable.tournament', function($q) {
                $q->where('status', 'accepted')->where('is_published', true);
            })
            ->where('points_max', '>=', $user->points)
            ->where('points_min', '<=', $user->points) // Respect de la borne basse
            ->whereDoesntHave('registrations', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['superTable.tournament'])
            ->get();

        $totalToPay = $myRegistrations->where('status', 'confirmed')->sum(function($reg) {
            return $reg->subTable->entry_fee;
        });

        return view('dashboard', compact('myRegistrations', 'availableSubTables', 'totalToPay'));
    }

    public function register(SubTable $subTable)
    {
        $user = Auth::user();

        // 1. Vérification des points (Snapshot de sécurité)
        if ($user->points > $subTable->points_max || $user->points < $subTable->points_min) {
            return back()->with('error', 'Ton classement ne correspond pas à ce tableau.');
        }

        // 2. Vérification du créneau horaire (Utilisation de la SuperTable)
        $alreadyInSlot = Registration::where('user_id', $user->id)
            ->whereHas('subTable', function($q) use ($subTable) {
                $q->where('super_table_id', $subTable->super_table_id);
            })->exists();

        if ($alreadyInSlot) {
            return back()->with('error', 'Tu as déjà un tableau prévu sur ce créneau.');
        }

        // 3. Gestion de la capacité (Automatique grâce au Model SuperTable)
        $status = $subTable->superTable->isFull() ? 'waiting_list' : 'confirmed';

        // 4. Inscription avec Snapshot
        Registration::create([
            'user_id'          => $user->id,
            'created_by'       => $user->id,
            'sub_table_id'     => $subTable->id,
            'player_license'   => $user->license_number,
            'player_firstname' => $user->first_name,
            'player_lastname'  => $user->last_name,
            'player_points'    => $user->points,
            'status'           => $status,
            'priority'         => 'primary',
        ]);

        $message = ($status === 'confirmed') ? 'Inscription validée !' : 'Créneau complet : tu es en liste d\'attente.';
        return back()->with('success', $message);
    }

    public function unregister(SubTable $subTable)
    {
        Registration::where('user_id', auth()->id())
            ->where('sub_table_id', $subTable->id)
            ->delete();

        return back()->with('success', 'Désinscription effectuée.');
    }

    public function redirectBasedOnRole()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return redirect()->route('admin.tournaments.index');
        }

        if ($user->isCoach()) {
            return redirect()->route('coach.dashboard');
        }

        return redirect()->route('dashboard');
    }
}