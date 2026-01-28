<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class TournamentController extends Controller
{
    public function index()
    {
        // Si super_admin, voit tout. Si admin, ne voit que ses tournois.
        $query = Tournament::orderBy('date', 'desc');

        if (!auth()->user()->isSuperAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $tournaments = $query->get();
        return view('admin.tournaments.index', compact('tournaments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|after:today', // On n'organise pas dans le passé !
            'location' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'registration_deadline' => 'required|date|before:date',
        ]);

        // On enrichit les données
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($request->name . '-' . rand(100, 999));
        
        // Par défaut, un tournoi est 'pending' (en attente) à la création
        $validated['status'] = 'pending';

        $tournament = Tournament::create($validated);

        return redirect()->route('admin.tournaments.show', $tournament)
                         ->with('success', 'Votre demande de tournoi a été soumise pour validation !');
    }

    /**
     * Méthode spécifique pour le SuperAdmin : Valider un tournoi
     */
    public function approve(Tournament $tournament)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $tournament->update(['status' => 'accepted']);

        // C'est ici qu'on déclenchera l'envoi du mail automatique au demandeur
        // Mail::to($tournament->contact_email)->send(new TournamentApproved($tournament));

        return back()->with('success', 'Le tournoi est désormais validé et visible !');
    }
}