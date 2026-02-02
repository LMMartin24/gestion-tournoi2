@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black relative flex items-center">
    
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-20 grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-black via-transparent to-black"></div>
    </div>

    <div class="relative z-10 max-w-4xl mt-12 mx-auto px-6 w-full">
        <div class="bg-[#1a1a1a] border border-white/10 p-8 md:p-12 rounded-3xl shadow-2xl">
            
            <div class="mb-10 text-center md:text-left">
                <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                    Inscription <span class="text-indigo-500 text-sm not-italic ml-2 tracking-widest">// SAISIE MANUELLE</span>
                </h2>
                <p class="text-white text-sm mt-2 uppercase tracking-widest">Édition 2026 — Ouistreham</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                {{-- Type de compte --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <div>
                        <x-input-label for="role" :value="__('Vous êtes ?')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <select id="role" name="role" class="block w-full bg-black/50 border-white/10 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm py-3 transition-all">
                            <option value="player">{{ __('Joueur (Individuel)') }}</option>
                            <option value="coach">{{ __('Inscription Groupée') }}</option>
                        </select>
                    </div>
                    <div id="license_container">
                        <x-input-label for="license_number" :value="__('Numéro de licence')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="license_number" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="text" name="license_number" :value="old('license_number')" required placeholder="Ex: 1412345" />
                    </div>
                </div>

                {{-- Infos Joueur --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-white/5 pt-6">
                    <div>
                        <x-input-label for="first_name" :value="__('Prénom')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="first_name" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="text" name="first_name" :value="old('first_name')" required />
                    </div>
                    <div>
                        <x-input-label for="last_name" :value="__('Nom')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="last_name" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="text" name="last_name" :value="old('last_name')" required />
                    </div>
                    <div>
                        <x-input-label for="club" :value="__('Club')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="club" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="text" name="club" :value="old('club')" required placeholder="Nom de votre club" />
                    </div>
                    <div id="points_container">
                        <x-input-label for="points" :value="__('Points mensuels')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="points" class="block w-full bg-black/50 border-white/10 text-indigo-400 font-bold rounded-xl py-3 text-lg" type="number" name="points" :value="old('points', 500)" required />
                        <p class="text-[9px] text-gray-500 mt-1 uppercase tracking-tighter">Points officiels au 1er du mois</p>
                    </div>
                </div>

                {{-- Champ caché pour le nom complet requis par Breeze --}}
                <input type="hidden" name="name" id="name" value="">

                {{-- Contact et Sécurité --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-white/5 pt-6">
                    <div class="md:col-span-1">
                        <x-input-label for="email" :value="__('Email')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="email" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="email" name="email" :value="old('email')" required />
                    </div>
                    <div class="md:col-span-1">
                        <x-input-label for="phone" :value="__('Téléphone')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="phone" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="text" name="phone" :value="old('phone')" required placeholder="06..." />
                    </div>
                    <div>
                        <x-input-label for="password" :value="__('Mot de passe')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="password" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="password" name="password" required />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirmer')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="password_confirmation" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="password" name="password_confirmation" required />
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between mt-10 pt-6 border-t border-white/5 gap-6">
                    <a class="text-xs uppercase tracking-widest text-gray-400 hover:text-white transition" href="{{ route('login') }}">
                        {{ __('Déjà inscrit ? Connexion') }}
                    </a>

                    <button type="submit" class="w-full md:w-auto bg-white text-black font-black uppercase text-xs tracking-[0.2em] py-4 px-10 rounded-full hover:bg-indigo-500 hover:text-white transition-all duration-300 transform hover:scale-105">
                        {{ __('Créer mon compte') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const firstName = document.getElementById('first_name');
    const lastName = document.getElementById('last_name');
    const hiddenName = document.getElementById('name');
    const roleSelect = document.getElementById('role');
    const licenseCont = document.getElementById('license_container');
    const pointsCont = document.getElementById('points_container');
    const licenseInput = document.getElementById('license_number');
    const pointsInput = document.getElementById('points');
    const form = document.querySelector('form');

    // Gestion de l'affichage selon le rôle
    roleSelect.addEventListener('change', function() {
        if (this.value === 'coach') {
            licenseCont.style.opacity = '0.7';
            pointsCont.style.opacity = '0.7';
            licenseInput.required = false;
            pointsInput.required = false;
        } else {
            licenseCont.style.opacity = '1';
            pointsCont.style.opacity = '1';
            licenseInput.required = true;
            pointsInput.required = true;
        }
    });

    // Concaténation pour le contrôleur
    form.addEventListener('submit', function() {
        hiddenName.value = firstName.value + ' ' + lastName.value;
    });
</script>
@endsection