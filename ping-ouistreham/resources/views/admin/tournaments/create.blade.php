@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0a0a] text-white pt-24 pb-12">
    <div class="max-w-3xl mx-auto px-6">
        
        <div class="mb-10">
            <a href="{{ route('admin.tournaments.index') }}" class="text-gray-500 hover:text-white text-xs uppercase font-black tracking-widest flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor text-indigo-500">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-4xl font-[1000] uppercase italic tracking-tighter">Créer un <span class="text-indigo-500">Tournoi</span></h1>
        </div>

        {{-- AFFICHAGE DES ERREURS --}}
        @if ($errors->any())
            <div class="mb-8 bg-red-500/10 border border-red-500/20 p-6 rounded-2xl">
                <div class="flex items-center gap-3 mb-3 text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-black uppercase italic">Erreur de validation</span>
                </div>
                <ul class="text-red-400/80 text-sm space-y-1 ml-8 list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.tournaments.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-[#111] border border-white/5 p-8 rounded-[2rem] space-y-6">
                
                {{-- NOM DU TOURNOI --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Nom de l'évènement</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-0 transition-all"
                        placeholder="Ex: National Tennis de Table 2026">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- DATE DU TOURNOI --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Date du tournoi</label>
                        <input type="date" name="date" value="{{ old('date') }}" required 
                            class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-0 transition-all">
                    </div>

                    {{-- DATE LIMITE --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Limite d'inscription</label>
                        <input type="date" name="registration_deadline" value="{{ old('registration_deadline') }}" required 
                            class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-0 transition-all">
                    </div>
                </div>

                {{-- LIEU --}}
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Lieu / Ville</label>
                    <input type="text" name="location" value="{{ old('location') }}" required 
                        class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-0 transition-all"
                        placeholder="Ex: Gymnase municipal, Caen">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- EMAIL --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Email de contact</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email') }}" required 
                            class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-0 transition-all"
                            placeholder="organisateur@club.fr">
                    </div>

                    {{-- POINTS MAX (Optionnel) --}}
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Points Max Autorisés</label>
                        <input type="number" name="max_points_allowed" value="{{ old('max_points_allowed') }}" 
                            class="w-full bg-black border-white/10 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:ring-0 transition-all"
                            placeholder="Ex: 2000">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-black py-5 rounded-2xl uppercase italic tracking-widest transition-all shadow-xl shadow-indigo-600/20">
                Créer et passer à la configuration
            </button>
        </form>
    </div>
</div>
@endsection