<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuperTable;
use App\Models\SubTable;
use Illuminate\Http\Request;

class SubTableController extends Controller
{
    /**
     * Affiche le formulaire pour ajouter une série à un bloc.
     */
    public function create(SuperTable $superTable)
    {
        $tournament = $superTable->tournament;

        // SÉCURITÉ : Vérification du propriétaire
        if (auth()->id() !== $tournament->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        return view('admin.sub_tables.create', compact('superTable', 'tournament'));
    }

    /**
     * Enregistre la série.
     */
    public function store(Request $request, SuperTable $superTable)
    {
        $tournament = $superTable->tournament;

        if (auth()->id() !== $tournament->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'label'      => 'required|string|max:50', // "Série A" ou "Moins de 900"
            'entry_fee'  => 'required|numeric|min:0',
            'points_min' => 'required|integer|min:500',
            'points_max' => 'required|integer|gte:points_min',
        ]);

        // On crée la sub_table (la série) liée au bloc horaire
        $superTable->subTables()->create($validated);

        // UX : On redirige vers la vue de gestion du tournoi pour voir la liste mise à jour
        return redirect()->route('admin.tournaments.show', $tournament->slug)
                        ->with('success', "Série {$validated['label']} ajoutée avec succès.");
    }

    /**
     * Supprime une série.
     */
    public function destroy(SubTable $subTable)
    {
        $tournament = $subTable->superTable->tournament;

        if (auth()->id() !== $tournament->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $subTable->delete();

        return redirect()->back()->with('success', 'Série supprimée avec succès.');
    }
}