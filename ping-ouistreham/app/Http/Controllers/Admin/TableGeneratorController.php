<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuperTable;
use App\Models\Tournament;

class TableGeneratorController extends Controller
{
    public function index(Tournament $tournament)
    {
        // On récupère les créneaux avec leurs séries et les joueurs inscrits
        $superTables = SuperTable::where('tournament_id', $tournament->id)
            ->with(['subTables.users'])
            ->get();

        return view('admin.tables.index', compact('tournament', 'superTables'));
    }

    public function generate(SuperTable $superTable)
    {
        $tournament = $superTable->tournament;
        $fileName = 'Tableau_' . str_replace(' ', '_', $superTable->label) . '.csv';

        // Chargement des données avec les joueurs triés par points
        $superTable->load(['subTables.users' => function($query) {
            $query->orderBy('points', 'desc');
        }]);

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($superTable, $tournament) {
            $file = fopen('php://output', 'w');
            
            // Ajout du BOM pour qu'Excel reconnaisse l'UTF-8 (gère les accents)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Ligne 1 : Titre du tournoi
            fputcsv($file, ['TOURNOI', $tournament->name]);
            // Ligne 2 : Nom du Super Tableau (Créneau)
            fputcsv($file, ['CRÉNEAU', $superTable->label, 'Horaire', $superTable->start_time]);
            fputcsv($file, []); // Ligne vide de séparation

            // Parcours des sous-tableaux (Séries)
            foreach ($superTable->subTables as $subTable) {
                // Entête de la série
                fputcsv($file, ['SÉRIE', $subTable->label, 'Max pts', $subTable->points_max]);
                // Entête des colonnes
                fputcsv($file, ['Nom du Joueur', 'Points', 'N° Licence', 'Email']);

                foreach ($subTable->users as $player) {
                    fputcsv($file, [
                        $player->name,
                        $player->points,
                        $player->license_number,
                        $player->email
                    ]);
                }
                
                fputcsv($file, []); // Ligne vide entre chaque série
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}