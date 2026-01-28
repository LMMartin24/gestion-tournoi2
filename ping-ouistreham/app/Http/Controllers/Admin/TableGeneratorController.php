<?php 

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuperTable;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TableGeneratorController extends Controller
{
    public function index(Tournament $tournament)
    {
        // On vérifie que l'admin est le proprio
        if (auth()->id() !== $tournament->user_id && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $superTables = SuperTable::where('tournament_id', $tournament->id)
            ->with(['subTables.registrations']) // On charge les inscriptions, pas juste les users
            ->get();

        return view('admin.tables.index', compact('tournament', 'superTables'));
    }

    public function generate(SuperTable $superTable)
    {
        $tournament = $superTable->tournament;
        
        // Utilise 'name' car 'label' n'existe pas dans ta migration SuperTable
        $fileName = 'Export_' . Str::slug($superTable->name) . '.csv';

        // On charge les inscriptions confirmées, triées par points (snapshot)
        $superTable->load(['subTables.registrations' => function($query) {
            $query->where('status', 'confirmed')->orderBy('player_points', 'desc');
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
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($file, ['TOURNOI', $tournament->name, 'DATE', $tournament->date]);
            fputcsv($file, ['CRÉNEAU', $superTable->name, 'HORAIRE', $superTable->start_time]);
            fputcsv($file, []); 

            foreach ($superTable->subTables as $subTable) {
                fputcsv($file, ['SÉRIE', $subTable->label, 'LIMITE', $subTable->points_max . ' pts']);
                
                // Entête adaptée au logiciel de Juge-Arbitrage (SPID / Girpe)
                fputcsv($file, ['Place', 'Nom', 'Prénom', 'Points', 'Licence', 'Statut']);

                foreach ($subTable->registrations as $index => $reg) {
                    fputcsv($file, [
                        $index + 1,
                        $reg->player_lastname,
                        $reg->player_firstname,
                        $reg->player_points,
                        $reg->player_license,
                        $reg->priority == 'backup' ? 'Remplaçant' : 'Titulaire'
                    ]);
                }
                
                fputcsv($file, []); 
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}