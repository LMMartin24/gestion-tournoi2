@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0a0a] text-white pt-24 pb-12">
    <div class="max-w-3xl mx-auto px-6">
        
        {{-- RETOUR --}}
        <div class="mb-8">
            <a href="{{ route('admin.tournaments.index') }}" class="text-gray-500 hover:text-white transition-colors flex items-center gap-2 text-sm font-bold uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Retour à la gestion
            </a>
        </div>

        {{-- HEADER --}}
        <div class="mb-12">
            <h1 class="text-4xl md:text-5xl font-[1000] uppercase italic tracking-tighter">
                Modifier le <span class="text-indigo-500">Tournoi</span>
            </h1>
            <p class="text-gray-400 mt-2 uppercase tracking-widest text-xs font-bold">
                ID: {{ $tournament->id }} — {{ $tournament->name }}
            </p>
        </div>

        {{-- FORMULAIRE --}}
        <form action="{{ route('admin.tournaments.update', $tournament->slug) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-[#111] border border-white/5 rounded-[2rem] p-8 md:p-10 space-y-8">
                
                {{-- CHAMP : NOM --}}
                <div class="space-y-2">
                    <label for="name" class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-4">Nom du tournoi</label>
                    <input type="text" name="name" id="name" 
                        value="{{ old('name', $tournament->name) }}"
                        class="w-full bg-[#0a0a0a] border-2 border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:border-indigo-500 outline-none transition-all">
                    @error('name') <p class="text-red-500 text-xs font-bold mt-1 ml-4">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- CHAMP : LOCALISATION --}}
                    <div class="space-y-2">
                        <label for="location" class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-4">Localisation</label>
                        <input type="text" name="location" id="location" 
                            value="{{ old('location', $tournament->location) }}"
                            class="w-full bg-[#0a0a0a] border-2 border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:border-indigo-500 outline-none transition-all">
                        @error('location') <p class="text-red-500 text-xs font-bold mt-1 ml-4">{{ $message }}</p> @enderror
                    </div>

                    {{-- CHAMP : DATE (NOMMÉ 'date' POUR MATCH LE CONTROLLER) --}}
                    <div class="space-y-2">
                        <label for="date" class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-4">Date du tournoi</label>
                        <input type="date" name="date" id="date" 
                            value="{{ old('date', $tournament->date instanceof \DateTime ? $tournament->date->format('Y-m-d') : substr($tournament->date, 0, 10)) }}"
                            class="w-full bg-[#0a0a0a] border-2 border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:border-indigo-500 outline-none transition-all">
                        @error('date') <p class="text-red-500 text-xs font-bold mt-1 ml-4">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- CHAMP : EMAIL DE CONTACT --}}
                    <div class="space-y-2">
                        <label for="contact_email" class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-4">Email de contact</label>
                        <input type="email" name="contact_email" id="contact_email" 
                            value="{{ old('contact_email', $tournament->contact_email) }}"
                            class="w-full bg-[#0a0a0a] border-2 border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:border-indigo-500 outline-none transition-all">
                        @error('contact_email') <p class="text-red-500 text-xs font-bold mt-1 ml-4">{{ $message }}</p> @enderror
                    </div>

                    {{-- CHAMP : DATE LIMITE INSCRIPTION --}}
                    <div class="space-y-2">
                        <label for="registration_deadline" class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-4">Limite d'inscription</label>
                        <input type="date" name="registration_deadline" id="registration_deadline" 
                            value="{{ old('registration_deadline', $tournament->registration_deadline instanceof \DateTime ? $tournament->registration_deadline->format('Y-m-d') : substr($tournament->registration_deadline, 0, 10)) }}"
                            class="w-full bg-[#0a0a0a] border-2 border-white/5 rounded-2xl px-6 py-4 text-white font-bold focus:border-indigo-500 outline-none transition-all">
                        @error('registration_deadline') <p class="text-red-500 text-xs font-bold mt-1 ml-4">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- VISIBILITÉ --}}
                <div class="flex items-center gap-4 p-4 bg-[#0a0a0a] rounded-2xl border border-white/5">
                    <div class="flex-1">
                        <h4 class="text-xs font-black uppercase tracking-widest italic text-white">Publier le tournoi</h4>
                        <p class="text-gray-600 text-[10px] font-bold uppercase mt-1">Rendre visible par tous les joueurs</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" class="sr-only peer" {{ old('is_published', $tournament->is_published) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

            </div>

            {{-- BOUTONS --}}
            <div class="flex flex-col md:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 bg-white text-black font-[1000] py-5 rounded-2xl uppercase tracking-[0.2em] text-sm hover:bg-indigo-500 hover:text-white transition-all transform active:scale-95 shadow-xl shadow-white/5">
                    Mettre à jour le tournoi
                </button>
                <a href="{{ route('admin.tournaments.index') }}" class="px-10 py-5 bg-[#111] text-gray-500 font-black rounded-2xl uppercase tracking-widest text-sm text-center border border-white/5 hover:bg-red-600/10 hover:text-red-500 transition-all">
                    Annuler
                </a>
            </div>

        </form>
    </div>
</div>
@endsection