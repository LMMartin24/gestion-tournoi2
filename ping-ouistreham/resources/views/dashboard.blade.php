@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black text-white">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="mb-16">
            <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-8 flex items-center gap-3">
                <span class="w-2 h-8 bg-green-500 rounded-full"></span>
                Mes Tableaux <span class="text-gray-600 text-sm ml-2">({{ $mySubTables->count() }})</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($mySubTables as $table)
                    <div class="bg-[#0f0f0f] border border-green-500/20 p-6 rounded-[2rem] relative overflow-hidden group">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-black uppercase italic">{{ $table->label }}</h3>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                    Début : {{ \Carbon\Carbon::parse($table->superTable->start_time)->format('H:i') }}
                                </p>
                            </div>
                            <span class="bg-green-500/10 text-green-500 text-[10px] font-black px-3 py-1 rounded-full uppercase">Inscrit</span>
                        </div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="text-[10px] text-gray-400 font-bold uppercase">Points : <span class="text-white">{{ $table->points_max }}</span></div>
                            <div class="text-[10px] text-gray-400 font-bold uppercase">Frais : <span class="text-white">{{ $table->entry_fee }}€</span></div>
                        </div>

                        <form action="{{ route('player.unregister', $table->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full py-3 bg-white/5 hover:bg-red-500/10 text-gray-500 hover:text-red-500 border border-white/5 hover:border-red-500/20 rounded-xl font-black uppercase text-[10px] tracking-widest transition-all">
                                Se désinscrire
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center bg-[#0a0a0a] border border-white/5 rounded-[2rem]">
                        <p class="text-gray-600 font-black uppercase text-xs tracking-widest italic">Tu n'es inscrit à aucun tableau pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-8 flex items-center gap-3">
                <span class="w-2 h-8 bg-indigo-500 rounded-full"></span>
                Tableaux Disponibles
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableSubTables as $table)
                    <div class="bg-[#0f0f0f] border border-white/5 p-6 rounded-[2rem] hover:border-indigo-500/30 transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-black uppercase italic group-hover:text-indigo-400 transition-colors">{{ $table->label }}</h3>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest italic">
                                    Horaire : {{ \Carbon\Carbon::parse($table->superTable->start_time)->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="block text-lg font-black text-indigo-500">{{ $table->entry_fee }}€</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-black rounded-xl border border-white/5">
                                <span class="text-[10px] text-gray-500 font-black uppercase">Limite Classement</span>
                                <span class="text-sm font-black italic">{{ $table->points_max }} pts</span>
                            </div>

                            @php
                                // 1. Calculer si le créneau global est plein
                                $currentTotal = $table->superTable->subTables->sum(fn($s) => $s->users->count());
                                $isSuperFull = $currentTotal >= $table->superTable->max_players;
                                
                                // 2. Vérifier si le joueur a déjà 2 tableaux
                                $hasReachedLimit = auth()->user()->subTables->count() >= 2;
                            @endphp

                            {{-- FORMULAIRE D'INSCRIPTION --}}
                            <form action="{{ route('player.register', $table->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    {{ ($isSuperFull || $hasReachedLimit) ? 'disabled' : '' }} 
                                    class="w-full py-4 rounded-xl font-black uppercase text-[10px] tracking-[0.2em] transition-all
                                    {{ ($isSuperFull || $hasReachedLimit) 
                                        ? 'bg-gray-800 text-gray-500 cursor-not-allowed' 
                                        : 'bg-indigo-600 hover:bg-white hover:text-black text-white shadow-lg shadow-indigo-500/10' 
                                    }}">
                                    
                                    @if($isSuperFull)
                                        Créneau Complet
                                    @elseif($hasReachedLimit)
                                        Limite 2 Tableaux Atteinte
                                    @else
                                        S'inscrire
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection