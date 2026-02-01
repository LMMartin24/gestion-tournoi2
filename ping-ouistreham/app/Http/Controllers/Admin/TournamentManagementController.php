<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\SubTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TournamentManagementController extends Controller
{
    /**
     * Dashboard de gestion d'un tournoi spécifique
     */
    public function manage(Tournament $tournament)
    {
        // On charge tout pour éviter les requêtes N+1
        $tournament->load('superTables.subTables.registrations.user');
        return view('admin.tournaments.manage', compact('tournament'));
    }

    /**
     * Export CSV format GIRPE
     */
    public function exportGirpe(SubTable $subTable)
    {
        $fileName = 'GIRPE_' . Str::slug($subTable->label) . '_' . now()->format('d-m-Y') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function() use($subTable) {
            $file = fopen('php://output', 'w');
            // BOM pour l'encodage Excel/GIRPE
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Entêtes GIRPE standard
            fputcsv($file, ['Licence', 'Nom', 'Prenom', 'Points', 'Club'], ';');

            foreach ($subTable->registrations as $reg) {
                $user = $reg->user;
                // On nettoie le nom/prénom si besoin
                $nameParts = explode(' ', $user->name, 2);
                $nom = strtoupper($nameParts[0] ?? '');
                $prenom = ucfirst($nameParts[1] ?? '');

                fputcsv($file, [
                    $user->license_number,
                    $nom,
                    $prenom,
                    $reg->player_points, // On utilise le snapshot des points à l'inscription
                    $user->club ?? 'SANS CLUB'
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}