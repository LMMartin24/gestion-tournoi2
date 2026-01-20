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

                    <div class="flex gap-4 mt-6 md:mt-0">
                        <a href="{{ route('admin.super_tables.create', $tournament->id) }}" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded">
                            Ajouter un super tableau
                        </a>
                        
                        <a href="{{ route('admin.tournaments.edit', $tournament->id) }}" class="p-4 bg-white/5 text-gray-400 rounded-2xl hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
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