<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\SuperTable;
use App\Models\SubTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SuperTablesController extends Controller
{
    /**
     * Affiche la gestion des créneaux pour un tournoi spécifique.
     */
    public function create(Tournament $tournament)
    {
        // SÉCURITÉ : On vérifie que l'utilisateur est bien le proprio ou le SuperAdmin
        if (auth()->id() !== $tournament->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Action non autorisée sur ce tournoi.');
        }

        // On charge les relations pour éviter les requêtes N+1 en vue
        $tournament->load('superTables.subTables');

        return view('admin.super_tables.create', compact('tournament'));
    }

    /**
     * Enregistre un créneau (SuperTable).
     */
    public function store(Request $request, Tournament $tournament)
    {
        if (auth()->id() !== $tournament->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'start_time' => 'required',
            'max_players' => 'required|integer|min:1',
            'date' => 'nullable|date',
        ]);

        // On ajoute un nom par défaut si absent (ex: "Créneau de 14:00")
        $validated['name'] = $request->input('name', 'Créneau de ' . $request->start_time);
        
        if (empty($validated['date'])) {
            $validated['date'] = $tournament->date;
        }

        try {
            $tournament->superTables()->create($validated);
            return redirect()->back()->with('success', 'Bloc horaire ajouté au tournoi.');
        } catch (\Exception $e) {
            // Si ça rate encore, ce DD te dira quelle colonne SQL pose problème
            dd("Erreur SQL : " . $e->getMessage());
        }
    }

    /**
     * Supprime une SuperTable (et toutes ses SubTables par cascade).
     */
    public function destroy(SuperTable $superTable)
    {
        // SÉCURITÉ : Vérifier le propriétaire via le tournoi parent
        if (auth()->id() !== $superTable->tournament->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $superTable->delete();

        return redirect()->back()->with('success', 'Le créneau et ses tableaux ont été supprimés.');
    }

}