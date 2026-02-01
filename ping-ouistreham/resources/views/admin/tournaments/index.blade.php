@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black relative">
    <div class="max-w-6xl mx-auto px-6">
        
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                    Gestion des <span class="text-indigo-500">Tournois</span>
                </h2>
                <p class="text-gray-500 text-sm mt-2 uppercase tracking-widest font-bold">// LISTE ADMINISTRATIVE</p>
            </div>
            <a href="{{ route('admin.tournaments.create') }}" class="bg-white text-black px-6 py-3 rounded-full font-black uppercase text-xs tracking-widest hover:bg-indigo-500 hover:text-white transition">
                Créer un nouveau tournoi
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse($tournaments as $tournament)
                <div class="bg-[#1a1a1a] border border-white/10 p-8 rounded-3xl flex flex-col md:flex-row justify-between items-center group hover:border-indigo-500/50 transition-all duration-500">
                    <div class="flex items-center gap-8">
                        <div class="text-center bg-black/50 p-4 rounded-2xl border border-white/5 min-w-[100px]">
                            <span class="block text-2xl font-black text-white leading-none">{{ \Carbon\Carbon::parse($tournament->date)->format('d') }}</span>
                            <span class="block text-[10px] text-indigo-500 uppercase font-bold mt-1">{{ \Carbon\Carbon::parse($tournament->date)->translatedFormat('M Y') }}</span>
                        </div>
                        
                        <div>
                            <h3 class="text-2xl font-black text-white uppercase italic">{{ $tournament->name }}</h3>
                            <p class="text-gray-500 uppercase text-xs tracking-widest font-bold mt-1">📍 {{ $tournament->location }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6 md:mt-0">
                        {{-- Bouton principal --}}
                        <a href="{{ route('admin.tournaments.show', $tournament->slug) }}" 
                        class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-black uppercase italic tracking-widest text-xs hover:bg-indigo-500 transition shadow-lg shadow-indigo-600/20">
                            Gérer les tableaux
                        </a>
                        
                        {{-- Bouton d'édition --}}
                        <a href="{{ route('admin.tournaments.edit', $tournament->slug) }}" 
                        class="p-4 bg-white/5 text-gray-400 rounded-2xl hover:text-white hover:bg-white/10 transition border border-white/5" title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('admin.tournaments.export', $tournament->id) }}" 
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-bold uppercase text-xs transition-all shadow-lg shadow-emerald-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exporter les inscriptions
                        </a>
                        {{-- BOUTON DE SUPPRESSION --}}
                        <form action="{{ route('admin.tournaments.destroy', $tournament->slug) }}" method="POST" onsubmit="return confirm('Es-tu sûr de vouloir supprimer ce tournoi ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-4 bg-white/5 text-gray-500 rounded-2xl hover:text-red-500 hover:bg-red-500/10 transition border border-white/5 hover:border-red-500/20" title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 border-2 border-dashed border-white/5 rounded-3xl">
                    <p class="text-gray-600 uppercase font-bold tracking-widest">Aucun tournoi n'est programmé</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection