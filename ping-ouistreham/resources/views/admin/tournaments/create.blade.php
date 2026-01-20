@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black relative flex items-center">
    
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-10 grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-black via-transparent to-black"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-6 w-full">
        <div class="bg-[#1a1a1a] border border-white/10 p-8 md:p-12 rounded-3xl shadow-2xl">
            
            <div class="mb-12 flex justify-between items-end">
                <div>
                    <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                        Initialiser <span class="text-indigo-500">un Tournoi</span>
                    </h2>
                    <p class="text-gray-500 text-sm mt-2 uppercase tracking-widest font-bold">// ESPACE ADMINISTRATION</p>
                </div>
                <a href="{{ route('admin.tournaments.index') }}" class="text-xs text-gray-50 hover:text-white uppercase tracking-widest transition border-b border-white/10 pb-1">
                    Retour à la liste
                </a>
            </div>

            <form method="POST" action="{{ route('admin.tournaments.store') }}" class="space-y-8">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Nom de l\'événement')" class="text-gray-50 mb-3 uppercase text-xs tracking-[0.2em] font-bold" />
                    <x-text-input id="name" 
                        class="block w-full bg-black/50 border-white/10 text-white placeholder:text-gray-700 rounded-2xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-xl" 
                        type="text" name="name" :value="old('name')" 
                        required autofocus placeholder="Ex: OPEN DE NORMANDIE 2026" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-500 font-bold uppercase" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <x-input-label for="date" :value="__('Date du tournoi')" class="text-gray-50 mb-3 uppercase text-xs tracking-[0.2em] font-bold" />
                        <x-text-input id="date" 
                            class="block w-full bg-black/50 border-white/10 text-white rounded-2xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                            type="date" name="date" :value="old('date')" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2 text-xs text-red-500 font-bold uppercase" />
                    </div>

                    <div>
                        <x-input-label for="location" :value="__('Lieu')" class="text-gray-50 mb-3 uppercase text-xs tracking-[0.2em] font-bold" />
                        <x-text-input id="location" 
                            class="block w-full bg-black/50 border-white/10 text-white rounded-2xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                            type="text" name="location" :value="old('location', 'Ouistreham')" required />
                        <x-input-error :messages="$errors->get('location')" class="mt-2 text-xs text-red-500 font-bold uppercase" />
                    </div>
                </div>

                <div class="mt-8">
                    <x-input-label for="contact_email" :value="__('Email de contact')" class="text-gray-50 mb-3 uppercase text-xs tracking-[0.2em] font-bold" />
                    <x-text-input id="contact_email" 
                        class="block w-full bg-black/50 border-white/10 text-white rounded-2xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                        type="email" name="contact_email" :value="old('contact_email')" required />
                    <x-input-error :messages="$errors->get('contact_email')" class="mt-2 text-xs text-red-500 font-bold uppercase" />
                </div>

                <div class="pt-10 border-t border-white/5">
                    <button type="submit" class="w-full bg-white text-black font-black uppercase text-sm tracking-[0.3em] py-5 rounded-full hover:bg-indigo-600 hover:text-white transition-all duration-500 shadow-xl transform hover:-translate-y-1">
                        {{ __('Enregistrer et configurer les tableaux') }}
                    </button>
                    <p class="text-center text-gray-600 text-[10px] uppercase tracking-widest mt-6 font-bold">
                        Une fois créé, vous pourrez définir les catégories de points et les tarifs.
                    </p>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection