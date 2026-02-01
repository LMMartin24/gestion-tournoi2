@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black pt-32 pb-20 px-6">
    <div class="max-w-7xl mx-auto">
        
        <div class="mb-16 flex flex-col md:flex-row justify-between items-end gap-6">
            <div>
                <h2 class="text-5xl font-black uppercase italic tracking-tighter text-white">
                    MANAGEMENT <span class="text-indigo-500">D'ÉQUIPE</span>
                </h2>
                <p class="text-gray-500 text-xs mt-2 uppercase tracking-widest font-bold">
                    // {{ auth()->user()->club ?? 'SANS CLUB' }} — {{ auth()->user()->students->count() }} ÉLÈVES
                </p>
            </div>
            
            <a href="#add-student" class="bg-white/5 border border-white/10 text-white px-8 py-4 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-black transition-all">
                + Ajouter un nouvel élève
            </a>
        </div>

        <div class="mb-20">
            <h3 class="text-2xl font-black uppercase italic mb-8 text-white flex items-center gap-3">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                Inscrire mon équipe
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($availableSubTables as $table)
                    <div class="bg-[#0f0f0f] border border-white/5 p-8 rounded-[2.5rem] shadow-2xl flex flex-col group hover:border-indigo-500/30 transition-all">
                        
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-2xl font-black uppercase italic text-white group-hover:text-indigo-400 transition-colors">{{ $table->label }}</h3>
                                <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase italic tracking-widest">
                                    {{ \Carbon\Carbon::parse($table->superTable->start_time)->format('H:i') }} — {{ $table->entry_fee }}€
                                </p>
                            </div>
                            <span class="text-[10px] font-black px-3 py-1 bg-indigo-500/10 rounded-full text-indigo-500 border border-indigo-500/20 uppercase">
                                {{ $table->points_max }} pts
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-8 min-h-[32px]">
                            @php
                                $teamIds = auth()->user()->students->pluck('id')->push(auth()->id());
                                $teamInscribed = $table->users->whereIn('id', $teamIds);
                            @endphp

                            @foreach($teamInscribed as $inscribed)
                                <button type="button" 
                                    onclick="confirmUnregister('{{ $inscribed->id }}', '{{ $table->id }}', '{{ $inscribed->name }}')"
                                    class="flex items-center gap-2 text-[9px] bg-white/5 text-gray-400 border border-white/5 px-3 py-1.5 rounded-xl font-black uppercase tracking-widest hover:bg-red-500/20 hover:text-red-500 hover:border-red-500/30 transition-all group/badge">
                                    <span class="w-1 h-1 bg-green-500 rounded-full group-hover/badge:bg-red-500"></span>
                                    {{ $inscribed->name }}
                                    <span class="opacity-0 group-hover/badge:opacity-100 ml-1 text-xs">×</span>
                                </button>
                            @endforeach
                        </div>

                        <form action="{{ route('coach.register_player') }}" method="POST" class="mt-auto pt-6 border-t border-white/5">
                            @csrf
                            <input type="hidden" name="sub_table_id" value="{{ $table->id }}">
                            
                            <div class="space-y-4">
                                <select name="player_id" required class="w-full bg-black border border-white/10 rounded-2xl px-5 py-4 text-white text-[10px] font-black uppercase tracking-widest focus:border-indigo-500 outline-none transition-all">
                                    <option value="" disabled selected>Choisir un joueur...</option>
                                    @if(auth()->user()->points <= $table->points_max)
                                        <option value="{{ auth()->id() }}">Moi-même (Coach) - {{ auth()->user()->points }} pts</option>
                                    @endif
                                    @foreach(auth()->user()->students as $student)
                                        @php 
                                            $isFull = $student->subTables->count() >= 2;
                                            $tooManyPoints = $student->points > $table->points_max;
                                        @endphp
                                        <option value="{{ $student->id }}" {{ ($isFull || $tooManyPoints) ? 'disabled' : '' }}>
                                            {{ $student->name }} ({{ $student->points }} pts) {{ $isFull ? '[MAX 2]' : ($tooManyPoints ? '[POINTS TROP ÉLEVÉS]' : '') }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="submit" class="w-full bg-indigo-600 hover:bg-white hover:text-black text-white font-black uppercase text-[10px] tracking-[0.3em] py-5 rounded-2xl transition-all shadow-lg shadow-indigo-500/10">
                                    Valider l'inscription
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center border-2 border-dashed border-white/5 rounded-[3rem]">
                        <p class="text-gray-700 uppercase font-black tracking-[0.4em] text-sm italic">Aucune compétition ouverte</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div id="add-student" class="max-w-3xl mx-auto">
            <div class="bg-[#1a1a1a] border border-white/10 p-10 rounded-[3rem] shadow-3xl">
                <div class="mb-8">
                    <h2 class="text-3xl font-black uppercase italic text-white tracking-tighter">
                        NOUVEL <span class="text-indigo-500">ÉLÈVE</span>
                    </h2>
                    <p class="text-gray-600 text-[10px] uppercase font-bold tracking-widest mt-1 italic">// CRÉATION DE COMPTE AUTOMATIQUE</p>
                </div>

                <form action="{{ route('coach.add_student') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">Nom complet</label>
                            <input type="text" name="name" required placeholder="EX: JEAN DUPONT" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">N° Licence</label>
                            <input type="text" name="license_number" required placeholder="EX: 1422334" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">Points FFTT</label>
                            <input type="number" name="points" required placeholder="500" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">Email (ou celui du coach)</label>
                            <input type="email" name="email" required value="{{ auth()->user()->email }}" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-white text-black font-black uppercase text-xs tracking-[0.3em] py-6 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all duration-500 transform hover:-translate-y-1 shadow-xl">
                        Enregistrer dans mon groupe
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- Formulaire caché pour la désinscription --}}
<form id="unregister-form" action="{{ route('coach.unregister_player') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="player_id" id="unreg-player-id">
    <input type="hidden" name="sub_table_id" id="unreg-table-id">
</form>

<script>
function confirmUnregister(playerId, tableId, playerName) {
    if (confirm("Désinscrire " + playerName.toUpperCase() + " de ce tableau ?")) {
        document.getElementById('unreg-player-id').value = playerId;
        document.getElementById('unreg-table-id').value = tableId;
        document.getElementById('unregister-form').submit();
    }
}
</script>
@endsection