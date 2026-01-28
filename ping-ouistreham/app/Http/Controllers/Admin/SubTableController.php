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
            'label'      => 'required|string|max:255',
            'entry_fee'  => 'required|numeric|min:0',
            'points_min' => 'required|integer|min:500', // Ajouté pour les tableaux de niveau
            'points_max' => 'required|integer|gte:points_min', // Doit être > ou = au min
        ]);

        $superTable->subTables()->create($validated);

        // On redirige vers la gestion du tournoi (SuperTables) pour voir le résultat
        return redirect()->route('admin.super_tables.create', $tournament->id)
                         ->with('success', 'Série "' . $validated['label'] . '" ajoutée au bloc.');
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