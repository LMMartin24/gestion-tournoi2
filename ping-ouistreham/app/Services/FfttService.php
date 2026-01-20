<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FfttService
{
    public function getPlayerByLicense(string $license)
    {
        // --- MODE SIMULATION (En attendant tes accès officiels) ---
        if (config('app.env') === 'local') {
            usleep(500000); // On attend 0.5s pour simuler le délai de l'API

            // On simule une réussite pour ta licence ou n'importe quel numéro
            return [
                'nom'    => 'DUVAL',
                'prenom' => 'Julien',
                'points' => 1426,
                'club'   => 'OUISTREHAM AP'
            ];
        }

        // --- CODE RÉEL (Sera actif une fois sur le serveur de production) ---
        $ipid = env('FFTT_IPID');
        $key = env('FFTT_PASSWORD');
        $tm = now()->format('YmdHisv');
        $tmc = hash_hmac('sha1', $tm, sha1($key));

        $response = Http::get("https://www.fftt.com/wp-content/plugins/fftt-api/api/joueur_detail.php", [
            'serie'   => $ipid, 'tm' => $tm, 'tmc' => $tmc, 'licence' => $license,
        ]);

        if ($response->ok()) {
            $xml = simplexml_load_string($response->body());
            if ($xml && isset($xml->joueur->nom)) {
                return [
                    'nom'    => (string) $xml->joueur->nom,
                    'prenom' => (string) $xml->joueur->prenom,
                    'points' => (int) $xml->joueur->point,
                    'club'   => (string) $xml->joueur->club,
                ];
            }
        }
        return null;
    }
}