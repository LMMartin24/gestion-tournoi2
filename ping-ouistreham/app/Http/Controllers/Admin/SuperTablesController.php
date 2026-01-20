<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\SuperTable; // Importé
use App\Models\SubTable;   // Importé
use Illuminate\Http\Request;

class SuperTablesController extends Controller
{
    /**
     * Affiche la liste des créneaux et tableaux pour un tournoi.
     */
    public function create(Tournament $tournament)
    {
        // On charge les SuperTables avec leurs SubTables pour les afficher
        $superTables = $tournament->superTables()->with('subTables')->orderBy('start_time')->get();

        return view('admin.super_tables.create', compact('tournament', 'superTables'));
    }

    /**
     * Enregistre un créneau (SuperTable) et son premier tableau (SubTable).
     */
    public function store(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'max_players' => 'required|integer|min:1',
        ]);

        $tournament->superTables()->create($validated);

        return redirect()->back()->with('success', 'Super Tableau créé avec succès.');
    }

    /**
     * Supprime un sous-tableau spécifique.
     */
    public function destroy(SubTable $subTable)
    {
        $subTable->delete();

        return redirect()->back()->with('success', 'Le sous-tableau a été supprimé.');
    }
}