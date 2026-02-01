@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black relative">
    
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-5 grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-black via-black/80 to-black"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-6">
        
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <p class="text-indigo-500 text-xs uppercase tracking-[0.3em] font-bold mb-2">// Configuration Technique</p>
                <h2 class="text-5xl font-black text-white uppercase italic tracking-tighter">
                    {{ $tournament->name }}
                </h2>
                <p class="text-gray-500 mt-2 font-medium">Lieu : {{ $tournament->location }} | Date : {{ \Carbon\Carbon::parse($tournament->date)->format('d/m/Y') }}</p>
            </div>
            <a href="{{ route('admin.tournaments.index') }}" class="px-6 py-3 border border-white/10 text-white text-xs uppercase tracking-widest hover:bg-white hover:text-black transition-all rounded-full">
                Terminer la configuration
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-4">
                <div class="bg-[#111] border border-white/10 p-8 rounded-3xl sticky top-32">
                    <h3 class="text-white text-xl font-bold uppercase italic mb-6">Ajouter un <span class="text-indigo-500">Bloc Horaire</span></h3>
                    
                    <form method="POST" action="{{ route('admin.super_tables.store', $tournament->id) }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Nom du créneau')" class="text-gray-400 text-[10px] uppercase tracking-widest mb-2" />
                            <x-text-input id="name" class="block w-full bg-black border-white/10 text-white text-sm" type="text" name="name" placeholder="Ex: Samedi Matin" required />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_time" :value="__('Heure de début')" class="text-gray-400 text-[10px] uppercase tracking-widest mb-2" />
                                <x-text-input id="start_time" class="block w-full bg-black border-white/10 text-white text-sm" type="time" name="start_time" required />
                            </div>
                            <div>
                                <x-input-label for="max_players" :value="__('Capacité Max')" class="text-gray-400 text-[10px] uppercase tracking-widest mb-2" />
                                <x-text-input id="max_players" class="block w-full bg-black border-white/10 text-white text-sm" type="number" name="max_players" placeholder="72" required />
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold uppercase text-xs tracking-widest py-4 rounded-xl hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-500/20">
                            Créer le créneau
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-6">
                @forelse($tournament->superTables as $superTable)
                    <div class="bg-[#1a1a1a] border border-white/5 rounded-3xl overflow-hidden shadow-2xl">
                        <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/5">
                            <div>
                                <span class="bg-indigo-500 text-[10px] text-white px-3 py-1 rounded-full font-black uppercase tracking-tighter">{{ $superTable->start_time }}</span>
                                <h4 class="inline-block ml-3 text-white font-bold uppercase italic">{{ $superTable->name }}</h4>
                                <span class="ml-4 text-gray-500 text-xs">Capacité : {{ $superTable->max_players }} joueurs</span>
                            </div>
                            <form action="{{ route('admin.super_tables.destroy', $superTable->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-500/50 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($superTable->subTables as $subTable)
                                    <div class="bg-black/40 border border-white/5 p-4 rounded-2xl flex justify-between items-center group">
                                        <div>
                                            <p class="text-white font-bold text-sm">{{ $subTable->label }}</p>
                                            <p class="text-indigo-400 text-[10px] font-bold uppercase tracking-widest mt-1">{{ $subTable->points_max }} pts | {{ $subTable->entry_fee }}€</p>
                                        </div>
                                        <form action="{{ route('admin.sub_tables.destroy', $subTable->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="opacity-0 group-hover:opacity-100 text-gray-600 hover:text-red-500 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach

                                <a href="{{ route('admin.sub_tables.create', $superTable->id) }}" class="border-2 border-dashed border-white/10 rounded-2xl p-4 flex items-center justify-center hover:border-indigo-500 group transition-all">
                                    <span class="text-gray-500 group-hover:text-indigo-500 text-xs font-bold uppercase tracking-widest">+ Ajouter une série</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="border-2 border-dashed border-white/5 rounded-3xl p-20 text-center">
                        <p class="text-gray-600 uppercase tracking-[0.2em] font-bold text-sm">Aucun créneau configuré pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection