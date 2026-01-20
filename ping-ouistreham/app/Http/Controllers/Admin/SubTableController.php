<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\SuperTable;
use App\Models\SubTable;
use Illuminate\Http\Request;

class SubTableController extends Controller
{
    /**
     * Affiche le formulaire pour ajouter une série à un bloc précis.
     */
    public function create(SuperTable $superTable)
    {
        // On charge le tournoi pour le fil d'ariane ou les infos de contexte
        $tournament = $superTable->tournament;
        
        return view('admin.sub_tables.create', compact('superTable', 'tournament'));
    }

    /**
     * Enregistre la série dans la base de données.
     */
    public function store(Request $request, SuperTable $superTable)
    {
        $validated = $request->validate([
            'label'      => 'required|string|max:255',
            'entry_fee'  => 'required|numeric|min:0',
            'points_max' => 'required|integer|min:0',
        ]);

        $superTable->subTables()->create($validated);

        return redirect()->route('admin.super_tables.create', $superTable->tournament_id)
                        ->with('success', 'Série ajoutée au bloc.');
    }
    /**
     * Supprime une série.
     */
    public function destroy(SubTable $subTable)
    {
        $superTableId = $subTable->super_table_id;
        $subTable->delete();

        return redirect()->route('admin.sub_tables.create', $superTableId)
                        ->with('success', 'Série supprimée avec succès.');
    }
    public function subTables() {
        return $this->belongsToMany(SubTable::class, 'sub_table_user');
    }
}