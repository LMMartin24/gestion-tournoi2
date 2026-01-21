@extends('layouts.app')

@section('content')
<div class="min-h-screen  bg-black pt-24 pb-12 px-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- BLOC CRÉATION ÉLÈVE --}}
        <div class="max-w-4xl mt-12 mx-auto">
            <div class="bg-[#0f0f0f] border border-white/5 p-8 rounded-[2rem] shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-black uppercase italic text-white flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                        Créer un compte élève
                    </h2>
                </div>
                
                <form action="{{ route('coach.add_student') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-s uppercase font-black text-white  tracking-widest ml-1">Nom du joueur</label>
                            <input type="text" name="name" required class="bg-black border border-white/10 rounded-xl px-5 py-4 text-white text-sm outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-s uppercase font-black text-white  tracking-widest ml-1">N° Licence</label>
                            <input type="text" name="license_number" required class="bg-black border border-white/10 rounded-xl px-5 py-4 text-white text-sm outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-s uppercase font-black text-white  tracking-widest ml-1">Adresse Email</label>
                            <input type="email" name="email" required class="bg-black border border-white/10 rounded-xl px-5 py-4 text-white text-sm outline-none focus:border-indigo-500 transition-all">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-s uppercase font-black text-white  tracking-widest ml-1">Mot de passe provisoire</label>
                            <input type="password" name="password" required class="bg-black border border-white/10 rounded-xl px-5 py-4 text-white text-sm outline-none focus:border-indigo-500 transition-all">
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-8 bg-indigo-600 hover:bg-white hover:text-black text-white font-black uppercase text-[11px] tracking-[0.3em] py-5 rounded-2xl transition-all duration-300">
                        Créer le compte et lier l'élève
                    </button>
                </form>
            </div>

            {{-- LISTE ÉLÈVES --}}
            <div class="mt-12">
                <h3 class="text-white text-xl uppercase font-black italic mb-4 text-sm tracking-widest">Mes élèves enregistrés</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($myPlayers as $player)
                        <div class="bg-[#0f0f0f] border border-white/5 p-4 rounded-2xl flex justify-between items-center group">
                            <div class="flex flex-col">
                                <span class="text-white font-bold text-s uppercase group-hover:text-indigo-400">{{ $player->name }}</span>
                                <span class="text-gray-600 text-[15px]">{{ $player->email }}</span>
                            </div>
                            <span class="text-white  text-s font-mono">{{ $player->license_number }}</span>
                        </div>
                    @empty
                        <p class="text-white  text-xs uppercase py-8 text-center border border-dashed border-white/10 rounded-2xl col-span-2">Aucun élève</p>
                    @endforelse
                </div>
            </div>
        </div>

        <hr class="my-16 border-white/5">

        {{-- GRILLE DES TABLEAUX --}}
        <h2 class="text-2xl font-black uppercase italic mb-8 text-white flex items-center gap-3">
            <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
            Inscrire mon équipe
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($availableSubTables as $table)
                <div class="bg-[#0f0f0f] border border-white/5 p-6 rounded-[2rem] shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-black uppercase italic text-white">{{ $table->label }}</h3>
                            <span class="text-s font-bold px-3 py-1 bg-white/5 rounded-full text-gray-400 border border-white/5">
                                {{ $table->points_max }} pts max
                            </span>
                        </div>

                        {{-- Badges des inscrits CLIQUABLES pour désinscription --}}
                        <div class="flex flex-wrap gap-2 mb-6 min-h-[24px]">
                            @php
                                $teamIds = $myPlayers->pluck('id')->push(auth()->id());
                                $teamInscribed = $table->users->whereIn('id', $teamIds);
                            @endphp

                            @foreach($teamInscribed as $inscribed)
                                <button type="button" 
                                    onclick="confirmUnregister('{{ $inscribed->id }}', '{{ $table->id }}', '{{ $inscribed->name }}')"
                                    class="flex items-center gap-1.5 text-[9px] bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-2.5 py-1 rounded-lg font-black uppercase tracking-wider hover:bg-red-500/20 hover:text-red-400 hover:border-red-500/30 transition-all group/badge"
                                    title="Cliquer pour désinscrire">
                                    <span class="w-1 h-1 bg-indigo-400 rounded-full group-hover/badge:bg-red-400"></span>
                                    {{ $inscribed->name }}
                                    <span class="hidden group-hover/badge:inline ml-1 text-[12px]">&times;</span>
                                </button>
                            @endforeach
                        </div>

                        @include('partials.progression-bar', ['table' => $table])
                    </div>

                    <form action="{{ route('coach.register_player') }}" method="POST" class="mt-8 space-y-4 border-t border-white/5 pt-6">
                        @csrf
                        <input type="hidden" name="sub_table_id" value="{{ $table->id }}">
                        
                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] uppercase font-black text-white  tracking-widest ml-1">Sélectionner le joueur</label>
                            <select name="player_id" class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-white text-xs font-bold uppercase outline-none focus:border-indigo-500 transition-all cursor-pointer">
                                <option value="{{ auth()->id() }}">Moi-même (Coach)</option>
                                @foreach($myPlayers as $player)
                                    @php $isPlayerFull = $player->subTables->count() >= 2; @endphp
                                    <option value="{{ $player->id }}" {{ $isPlayerFull ? 'disabled' : '' }}>
                                        {{ $player->name }} {{ $isPlayerFull ? '(LIMITE 2/2)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @php
                            $currentTotal = $table->superTable->subTables->sum(fn($s) => $s->users->count());
                            $isSuperFull = $currentTotal >= $table->superTable->max_players;
                        @endphp

                        <button type="submit" {{ $isSuperFull ? 'disabled' : '' }}
                            class="w-full mt-4 {{ $isSuperFull ? 'bg-red-900/50 text-gray-400' : 'bg-indigo-600 hover:bg-white hover:text-black text-white' }} font-black uppercase text-s tracking-[0.2em] py-4 rounded-xl transition-all">
                            {{ $isSuperFull ? 'Complet (' . $currentTotal . '/' . $table->superTable->max_players . ')' : 'Valider l\'inscription' }}
                        </button>
                    </form>
                </div>
            @empty
                <div class="col-span-full py-20 text-center border border-dashed border-white/5 rounded-[3rem]">
                    <p class="text-gray-600 uppercase font-black tracking-[0.3em] text-sm">Aucun tableau disponible</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

{{-- Formulaire caché pour la désinscription --}}
<form id="unregister-form" action="{{ route('coach.unregister_player') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="player_id" id="unreg-player-id">
    <input type="hidden" name="sub_table_id" id="unreg-table-id">
</form>

<script>
function confirmUnregister(playerId, tableId, playerName) {
    if (confirm("Êtes-vous sûr de vouloir désinscrire " + playerName + " ?")) {
        document.getElementById('unreg-player-id').value = playerId;
        document.getElementById('unreg-table-id').value = tableId;
        document.getElementById('unregister-form').submit();
    }
}
</script>
@endsection