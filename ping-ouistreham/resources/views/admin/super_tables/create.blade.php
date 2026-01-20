@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black">
    <div class="max-w-5xl mx-auto px-6">
        
        <div class="mb-12 flex justify-between items-end">
            <div>
                <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                    Nouveau <span class="text-indigo-500">Bloc Horaire</span>
                </h2>
                <p class="text-gray-500 text-sm mt-2 uppercase tracking-widest font-bold">
                    Tournoi : {{ $tournament->name }}
                </p>
            </div>
            {{-- Lien retour vers le tournoi --}}
            <a href="{{ route('admin.tournaments.show', $tournament->id) }}" class="text-gray-400 hover:text-white text-[10px] font-black uppercase tracking-widest border-b border-white/10 pb-1">
                Retour au tournoi
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-[#1a1a1a] border border-white/10 p-6 rounded-3xl sticky top-32">
                    <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6">Créer un créneau</h3>
                    <form action="{{ route('admin.super_tables.store', $tournament->id) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Nom du bloc</label>
                            <input type="text" name="name" placeholder="Ex: Samedi Matin" required
                                class="w-full bg-black border border-white/10 text-white rounded-xl px-4 py-3 focus:border-indigo-500 transition-all outline-none">
                        </div>

                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Heure de début</label>
                            <input type="time" name="start_time" required
                                class="w-full bg-black border border-white/10 text-white rounded-xl px-4 py-3 focus:border-indigo-500 transition-all outline-none">
                        </div>

                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Capacité totale (joueurs)</label>
                            <input type="number" name="max_players" placeholder="64" required
                                class="w-full bg-black border border-white/10 text-white rounded-xl px-4 py-3 focus:border-indigo-500 transition-all outline-none">
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white font-black uppercase text-xs py-4 rounded-xl hover:bg-indigo-500 transition shadow-lg shadow-indigo-600/20">
                            Créer le bloc
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-4">Blocs configurés ({{ $tournament->superTables->count() }})</h3>
                
                @forelse($tournament->superTables as $st)
                    <div class="bg-white/5 border border-white/5 p-5 rounded-2xl flex justify-between items-center group hover:border-white/20 transition-all">
                        <div class="flex-1">
                            <p class="text-white font-bold text-lg italic uppercase">{{ $st->name }}</p>
                            <div class="flex gap-4 mt-1">
                                <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest">🕒 {{ \Carbon\Carbon::parse($st->start_time)->format('H:i') }}</span>
                                <span class="text-[10px] text-gray-500 uppercase tracking-widest">👥 {{ $st->max_players }} joueurs max</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.sub_tables.create', $st->id) }}" 
                               class="bg-white/10 hover:bg-white text-white hover:text-black px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-tighter transition-all">
                                + Ajouter une série
                            </a>

                            <form action="{{ route('admin.super_tables.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Supprimer ce bloc et toutes ses séries ?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-gray-600 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="border-2 border-dashed border-white/5 rounded-3xl p-12 text-center">
                        <p class="text-gray-600 uppercase text-xs font-bold tracking-widest">Aucun bloc horaire créé.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection