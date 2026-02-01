<?php

namespace App\Observers;

use App\Models\Registration;

class RegistrationObserver
{
    /**
     * Se déclenche APRÈS qu'une inscription a été supprimée.
     */
    public function deleted(Registration $registration)
    {
        // On ne repêche que si une place CONFIRMÉE a été libérée
        if ($registration->status !== 'confirmed') {
            return;
        }

        // On récupère le SuperTable via la relation (chargée si besoin)
        $subTable = $registration->subTable;
        if (!$subTable) return;
        
        $superTable = $subTable->superTable;
        if (!$superTable) return;

        // 1. On compte les places encore occupées
        $confirmedCount = Registration::whereHas('subTable', function($q) use ($superTable) {
                $q->where('super_table_id', $superTable->id);
            })
            ->where('status', 'confirmed')
            ->count();

        // 2. Si on est en dessous de la limite, on cherche le prochain
        if ($confirmedCount < (int)$superTable->max_players) {
            
            $nextInLine = Registration::whereHas('subTable', function($q) use ($superTable) {
                    $q->where('super_table_id', $superTable->id);
                })
                ->where('status', 'waiting_list')
                ->orderBy('created_at', 'asc') // Le plus ancien en premier
                ->first();

            if ($nextInLine) {
                // On utilise update pour déclencher d'éventuels autres événements
                $nextInLine->update(['status' => 'confirmed']);
            }
        }
    }
}