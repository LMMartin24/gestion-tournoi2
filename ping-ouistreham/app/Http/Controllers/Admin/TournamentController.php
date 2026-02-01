<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tournament;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TournamentController extends Controller
{
    public function index()
    {
        $query = Tournament::orderBy('date', 'desc');

        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $tournaments = $query->get();
        return view('admin.tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('admin.tournaments.create');
    }

    public function store(Request $request) 
    {
        // ÉTAPE A : Voir si les données arrivent bien du formulaire
        // dd($request->all()); 

        try {
            // ÉTAPE B : Validation manuelle pour voir si ça bloque ici
            $validated = $request->validate([
                'name' => 'required',
                'date' => 'required',
                'location' => 'required',
                'contact_email' => 'required|email',
                'registration_deadline' => 'required',
            ]);

            // ÉTAPE C : Préparation manuelle
            $tournament = new \App\Models\Tournament();
            $tournament->name = $request->name;
            $tournament->date = $request->date;
            $tournament->location = $request->location;
            $tournament->contact_email = $request->contact_email;
            $tournament->registration_deadline = $request->registration_deadline;
            $tournament->user_id = auth()->id();
            $tournament->slug = \Illuminate\Support\Str::slug($request->name) . '-' . rand(100, 999);
            $tournament->status = 'accepted';
            $tournament->is_published = true;

            // ÉTAPE D : Tentative d'insert
            $tournament->save();

            // Remplace ton dernier return par celui-ci :
            return redirect()->route('admin.tournaments.show', $tournament->slug)
                ->with('success', 'Tournoi créé avec succès ! Ajoute maintenant tes tableaux.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si ça bloque à la validation
            dd('❌ ERREUR VALIDATION :', $e->errors());
        } catch (\Exception $e) {
            // Si ça bloque à la base de données (SQL)
            dd('❌ ERREUR SQL :', $e->getMessage());
        }
    }

    public function show($slug)
    {
        $tournament = Tournament::where('slug', $slug)
            ->with(['superTables.subTables.registrations.user'])
            ->firstOrFail();

        if ($tournament->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Action non autorisée.');
        }

        return view('admin.tournaments.show', compact('tournament'));
    }



    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Tournament $tournament)
    {
        // On passe l'objet $tournament à la vue
        return view('admin.tournaments.edit', compact('tournament'));
    }

    /**
     * Enregistre les modifications en base de données
     */
    public function update(Request $request, Tournament $tournament)
    {
        // 1. Validation : On a retiré 'description'
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'date'          => 'required|date',
            'contact_email' => 'required|email',
            'registration_deadline' => 'required|date',
            'is_published'  => 'nullable|boolean',
        ]);

        // 2. Mise à jour du slug si le nom a changé
        if ($tournament->name !== $request->name) {
            $validated['slug'] = Str::slug($request->name) . '-' . rand(100, 999);
        }

        // Gestion du boolean pour la checkbox
        $validated['is_published'] = $request->has('is_published');

        // 3. Mise à jour en base (Laravel n'enverra que les champs présents dans $validated)
        $tournament->update($validated);

        return redirect()->route('admin.tournaments.index')
            ->with('success', 'Le tournoi a été mis à jour avec succès !');
    }

    public function destroy(Tournament $tournament)
    {
        // Sécurité : Vérifier si l'admin a le droit de supprimer ce tournoi
        if ($tournament->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $tournament->delete();

        return redirect()->route('admin.tournaments.index')
            ->with('success', 'Le tournoi a été supprimé avec succès.');
    }
    /**
     * Relation vers l'utilisateur (le joueur)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}