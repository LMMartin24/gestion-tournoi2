@php
    // 1. On récupère l'ID du SuperTable
    $superTableId = $table->super_table_id;

    // 2. COMPTAGE DIRECT EN BD (SANS PASSER PAR LES RELATIONS CACHÉES)
    // On compte combien d'entrées existent dans la table pivot pour TOUTES les séries de ce bloc
    $totalInscrits = DB::table('sub_table_user')
        ->whereIn('sub_table_id', function($query) use ($superTableId) {
            $query->select('id')
                  ->from('sub_tables')
                  ->where('super_table_id', $superTableId);
        })
        ->count();

    // 3. Récupération de la capacité réelle
    $maxPlaces = DB::table('super_tables')->where('id', $superTableId)->value('max_players') ?: 20;

    // 4. Calcul du pourcentage réel
    $percent = ($totalInscrits / $maxPlaces) * 100;

    // 5. Couleurs en Hexa (Indigo, Orange, Rouge)
    $color = '#6366f1'; 
    if ($percent >= 100) $color = '#ef4444';
    elseif ($percent > 80) $color = '#f97316';
@endphp

<div class="w-full mt-4">

    
    {{-- Rail --}}
    <div style="height: 6px; width: 100%; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
        {{-- Jauge --}}
        <div style="
            height: 100%; 
            width: {{ max(2, min(100, $percent)) }}%; 
            background-color: {{ $color }}; 
            box-shadow: 0 0 15px {{ $color }}88;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        "></div>
    </div>
</div>