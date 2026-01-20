<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FfttService; // <--- CETTE LIGNE EST MANQUANTE OU INCORRECTE
use Illuminate\Http\JsonResponse;

class FfttController extends Controller
{
    // L'injection de dépendance fonctionnera maintenant car Laravel sait où trouver FfttService
    public function verify(string $license, FfttService $fftt): JsonResponse
    {
        $player = $fftt->getPlayerByLicense($license);

        if ($player) {
            return response()->json(['success' => true, 'player' => $player]);
        }

        return response()->json(['success' => false, 'message' => 'Joueur non trouvé'], 404);
    }
}