@extends('layouts.app')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="min-h-screen pt-32 pb-20 bg-black" x-data="{ openSlotModal: false, openSeriesModal: false, activeSlotId: null }">
    <div class="max-w-7xl mx-auto px-6">
        
        {{-- AFFICHAGE DES ERREURS (Très important pour le debug) --}}
        @if ($errors->any())
            <div class="mb-8 p-4 bg-red-500/10 border border-red-500/50 rounded-2xl">
                <ul class="list-disc list-inside text-red-500 text-xs font-bold uppercase tracking-widest">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $tournament->status === 'accepted' ? 'bg-green-500/20 text-green-500' : 'bg-yellow-500/20 text-yellow-500' }}">
                        ● {{ $tournament->status }}
                    </span>
                    <span class="text-gray-600 text-[10px] font-black uppercase tracking-[0.3em]"> // CONFIGURATION</span>
                </div>
                <h2 class="text-5xl font-black text-white uppercase italic tracking-tighter">
                    {{ $tournament->name }}
                </h2>
            </div>
            
            <div class="flex gap-4">
                <button @click="openSlotModal = true" class="bg-white text-black px-8 py-4 rounded-full font-black uppercase text-[10px] tracking-widest hover:bg-indigo-600 hover:text-white transition-all transform hover:scale-105 shadow-xl">
                    + Ajouter un créneau
                </button>
            </div>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-indigo-600 rounded-[2rem] p-8 text-white shadow-xl shadow-indigo-500/20 relative overflow-hidden">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-70">Revenus Attendus</p>
                <h4 class="text-4xl font-black italic">
                    {{ number_format($tournament->superTables->flatMap->subTables->sum(function($sub) { 
                        return $sub->registrations->count() * $sub->entry_fee; 
                    }), 2) }} €
                </h4>
            </div>

            <div class="bg-[#1a1a1a] border border-white/10 rounded-[2rem] p-8">
                <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.2em] mb-2">Inscriptions Totales</p>
                <h4 class="text-4xl font-black text-white italic">
                    {{ $tournament->superTables->flatMap->subTables->flatMap->registrations->count() }}
                </h4>
            </div>

            <div class="bg-[#1a1a1a] border border-white/10 rounded-[2rem] p-8">
                <p class="text-[10px] text-gray-500 font-black uppercase tracking-[0.2em] mb-2">Clubs Représentés</p>
                <h4 class="text-4xl font-black text-white italic">
                    {{ $tournament->superTables->flatMap->subTables->flatMap->registrations->pluck('user.club_name')->unique()->filter()->count() }}
                </h4>
            </div>
        </div>

        {{-- LISTE DES CRÉNEAUX --}}
        <div class="space-y-10">
            @forelse($tournament->superTables as $slot)
                <div class="bg-[#111111] border border-white/5 rounded-[2.5rem] overflow-hidden">
                    <div class="bg-white/5 px-8 py-6 flex justify-between items-center border-b border-white/5">
                        <div class="flex items-center gap-6">
                            <div class="text-3xl font-black italic text-indigo-500">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest">{{ $slot->name }}</p>
                                <p class="text-white font-bold">{{ $slot->max_players }} joueurs max</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <button @click="openSeriesModal = true; activeSlotId = {{ $slot->id }}" class="text-[10px] font-black uppercase bg-indigo-600/10 text-indigo-400 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition">
                                + Ajouter une série
                            </button>
                            {{-- BOUTON SUPPRIMER --}}
                            <form action="{{ route('admin.super_tables.destroy', $slot->id) }}" method="POST" onsubmit="return confirm('Supprimer ce créneau et toutes ses séries ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-600 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($slot->subTables as $series)
                            <div class="bg-black/40 border border-white/10 p-6 rounded-2xl relative group hover:border-indigo-500/50 transition-all">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="text-xl font-black text-white uppercase italic">{{ $series->label }}</h4>
                                    <span class="text-indigo-500 font-mono text-sm">{{ $series->entry_fee }}€</span>
                                </div>
                                <div class="flex flex-col gap-1 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                    <span>Limite : {{ $series->points_max }} pts</span>
                                    <span>Inscrits : {{ $series->registrations->count() }}</span>
                                </div>
                                <div class="mt-6 pt-4 border-t border-white/5">
                                    <form action="{{ route('admin.sub_tables.destroy', $series->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500/50 hover:text-red-500 text-[9px] font-black uppercase italic transition">Supprimer la série</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-700 text-[10px] uppercase font-bold italic tracking-widest">Aucune série configurée.</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="py-20 text-center border-2 border-dashed border-white/5 rounded-[3rem]">
                    <p class="text-gray-600 font-black uppercase tracking-widest text-sm italic">Aucun créneau horaire.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL : CRÉNEAU (SUPER TABLE) --}}
    <div x-show="openSlotModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/90 backdrop-blur-sm">
        <div class="bg-[#111] border border-white/10 p-8 rounded-[2.5rem] max-w-md w-full shadow-2xl" @click.away="openSlotModal = false">
            <h3 class="text-2xl font-black text-white uppercase italic mb-6">Nouveau créneau</h3>
            <form action="{{ route('admin.super_tables.store', $tournament->slug) }}" method="POST" class="space-y-6">
                @csrf
                {{-- LE CHAMP NAME EST BIEN ICI MAINTENANT --}}
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Nom du créneau</label>
                    <input type="text" name="name" required class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 shadow-inner" placeholder="Ex: Matinée ou Tableau du Samedi">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Heure de début</label>
                    <input type="time" name="start_time" required class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 transition-all shadow-inner">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Capacité Max (Joueurs)</label>
                    <input type="number" name="max_players" required class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 transition-all shadow-inner" placeholder="Ex: 32">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="openSlotModal = false" class="flex-1 px-6 py-4 rounded-xl text-[10px] font-black uppercase text-gray-400 hover:bg-white/5 transition">Annuler</button>
                    <button type="submit" class="flex-1 bg-indigo-600 px-6 py-4 rounded-xl text-[10px] font-black uppercase text-white hover:bg-indigo-500 transition">Créer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL : SÉRIE (SUB TABLE) --}}
    <div x-show="openSeriesModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/90 backdrop-blur-sm">
        <div class="bg-[#111] border border-white/10 p-8 rounded-[2.5rem] max-w-md w-full shadow-2xl" @click.away="openSeriesModal = false">
            <h3 class="text-2xl font-black text-white uppercase italic mb-6">Ajouter une série</h3>
            <form :action="`/admin/super-tables/${activeSlotId}/sub-tables`" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Nom de la série</label>
                    <input type="text" name="label" required class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 shadow-inner" placeholder="Ex: Série A">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Points Min</label>
                        <input type="number" name="points_min" value="0" class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 shadow-inner">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Points Max</label>
                        <input type="number" name="points_max" required class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 shadow-inner">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Frais d'inscription (€)</label>
                    <input type="number" name="entry_fee" required class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 shadow-inner">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" @click="openSeriesModal = false" class="flex-1 px-6 py-4 rounded-xl text-[10px] font-black uppercase text-gray-400 hover:bg-white/5 transition">Annuler</button>
                    <button type="submit" class="flex-1 bg-white text-black px-6 py-4 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 hover:text-white transition">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection