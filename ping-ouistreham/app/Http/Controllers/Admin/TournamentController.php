<?php
namespace App\Http\Controllers\Admin;

use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TournamentController extends Controller
{
    /**
     * Affiche la liste des tournois (Page Index)
     */
    public function index()
    {
        $tournaments = Tournament::orderBy('date', 'desc')->get();
        return view('admin.tournaments.index', compact('tournaments'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.tournaments.create');
    }

    /**
     * Enregistre le tournoi dans la base de données
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'contact_email' => 'required|email', // Obligatoire suite à ton erreur précédente
        ]);

        $tournament = Tournament::create($validated);

        // Après la création, on redirige vers la liste avec un message de succès
        return redirect()->route('admin.tournaments.index')
                         ->with('success', 'Tournoi créé avec succès !');
    }

    /**
     * Les autres méthodes (show, edit, update, destroy) 
     * pourront être complétées plus tard.
     */
}