@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black relative flex items-center">
    
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-20 grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-black via-transparent to-black"></div>
    </div>

    <div class="relative z-10 max-w-4xl mt-12  mx-auto px-6 w-full">
        <div class="bg-[#1a1a1a] border border-white/10 p-8 md:p-12 rounded-3xl shadow-2xl">
            
            <div class="mb-10">
                <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                    Inscription <span class="text-indigo-500 text-sm not-italic ml-2 tracking-widest">// REJOINDRE LE TOURNOI</span>
                </h2>
                <p class="text-white text-sm mt-2 uppercase tracking-widest">Édition 2026 — Ouistreham</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6" id="register-form">
                @csrf

                <div>
                    <x-input-label for="role" :value="__('Type de compte')" class="text-white mb-2 uppercase text-xs tracking-widest"  />
                    <select id="role" name="role" class="block w-full bg-black/50 border-white/10 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm py-3">
                        <option value="player">{{ __('Joueur (Inscription individuelle)') }}</option>
                        <option value="coach">{{ __('Entraîneur (Inscriptions groupées)') }}</option>
                    </select>
                </div>

                <div id="license-section">
                    <x-input-label for="license_number" :value="__('Numéro de licence')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                    <div class="flex gap-3">
                        <x-text-input id="license_number" class="block w-full bg-black/50 border-white/10 text-white placeholder:text-white rounded-xl py-3" type="text" name="license_number" :value="old('license_number')" required />
                        <button type="button" id="btn-verify-license" 
                            class="px-8 bg-indigo-600 hover:bg-indigo-700 text-white font-bold uppercase text-[10px] tracking-[0.2em] rounded-xl transition duration-300">
                            {{ __('Vérifier') }}
                        </button>
                    </div>
                    <p id="verify-feedback" class="text-[10px] uppercase tracking-widest mt-2 font-bold"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="first_name" :value="__('Prénom')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="first_name" class="block w-full bg-white/5 border-white/5 text-gray-400 rounded-xl py-3 transition-colors" type="text" name="first_name" :value="old('first_name')" required readonly placeholder="Vérifiez votre licence" />
                    </div>
                    <div>
                        <x-input-label for="last_name" :value="__('Nom')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="last_name" class="block w-full bg-white/5 border-white/5 text-gray-400 rounded-xl py-3 transition-colors" type="text" name="last_name" :value="old('last_name')" required readonly />
                    </div>
                    <div id="club-container">
                        <x-input-label for="club" :value="__('Club')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="club" class="block w-full bg-white/5 border-white/5 text-gray-400 rounded-xl py-3 transition-colors" type="text" name="club" :value="old('club')" readonly />
                    </div>
                    <div id="points-section">
                        <x-input-label for="points" :value="__('Points officiels')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="points" class="block w-full bg-white/5 border-white/5 text-indigo-400 font-bold rounded-xl py-3 text-lg transition-colors" type="text" name="points" :value="old('points')" required readonly />
                    </div>
                </div>

                <input type="hidden" name="name" id="name" value="{{ old('name') }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-white/5 pt-6">
                    <div class="md:col-span-2">
                        <x-input-label for="email" :value="__('Email')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="email" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="email" name="email" :value="old('email')" required />
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('Téléphone')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="phone" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="text" name="phone" :value="old('phone')" required />
                    </div>
                    <div>
                        <x-input-label for="password" :value="__('Mot de passe')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="password" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="password" name="password" required />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-white mb-2 uppercase text-xs tracking-widest" />
                        <x-text-input id="password_confirmation" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-3" type="password" name="password_confirmation" required />
                    </div>
                </div>

                <div class="flex items-center justify-between mt-10 pt-6 border-t border-white/5">
                    <a class="text-xs uppercase tracking-widest text-white hover:text-white transition" href="{{ route('login') }}">
                        {{ __('Déjà inscrit ? Connexion') }}
                    </a>

                    <button type="submit" class="bg-white text-black font-black uppercase text-xs tracking-[0.2em] py-4 px-10 rounded-full hover:bg-indigo-500 hover:text-white transition-all duration-300">
                        {{ __('Créer mon compte') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const roleSelect = document.getElementById('role');
    const licenseSection = document.getElementById('license-section');
    const pointsSection = document.getElementById('points-section');
    
    const licenseInput = document.getElementById('license_number');
    const btnVerify = document.getElementById('btn-verify-license');
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const pointsInput = document.getElementById('points');
    const clubInput = document.getElementById('club');
    const feedback = document.getElementById('verify-feedback');
    const hiddenNameInput = document.getElementById('name');

    const updateHiddenName = () => {
        hiddenNameInput.value = `${firstNameInput.value} ${lastNameInput.value}`.trim();
    };

    firstNameInput.addEventListener('input', updateHiddenName);
    lastNameInput.addEventListener('input', updateHiddenName);

    roleSelect.addEventListener('change', function() {
        if (this.value === 'coach') {
            // MODE ENTRAÎNEUR
            licenseSection.classList.add('hidden');
            pointsSection.classList.add('hidden');
            
            licenseInput.required = false;
            pointsInput.required = false;
            
            licenseInput.value = "";
            pointsInput.value = "0"; // Stocké en base mais non affiché
            
            [firstNameInput, lastNameInput, clubInput].forEach(el => {
                el.readOnly = false;
                el.classList.remove('bg-white/5', 'text-gray-400');
                el.classList.add('bg-black/50', 'text-white');
                el.placeholder = "";
            });

            feedback.innerText = "";
        } else {
            // MODE JOUEUR
            licenseSection.classList.remove('hidden');
            pointsSection.classList.remove('hidden');
            
            licenseInput.required = true;
            pointsInput.required = true;
            
            [firstNameInput, lastNameInput, clubInput].forEach(el => {
                el.readOnly = true;
                el.classList.add('bg-white/5', 'text-gray-400');
                el.classList.remove('bg-black/50', 'text-white');
                el.value = "";
            });
            firstNameInput.placeholder = "Vérifiez votre licence";
            pointsInput.value = "";
        }
    });

    btnVerify.addEventListener('click', function() {
        const license = licenseInput.value;
        if (!license) return;

        btnVerify.disabled = true;
        feedback.innerText = 'Vérification...';
        feedback.className = 'text-[10px] uppercase tracking-widest mt-2 font-bold text-indigo-50 animate-pulse';

        fetch(`/verify-license/${license}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    firstNameInput.value = data.player.prenom;
                    lastNameInput.value = data.player.nom;
                    pointsInput.value = data.player.points;
                    clubInput.value = data.player.club;
                    updateHiddenName();
                    
                    feedback.innerText = 'Joueur identifié ✓';
                    feedback.className = 'text-[10px] uppercase tracking-widest mt-2 font-bold text-green-500';
                } else {
                    feedback.innerText = 'Licence introuvable ✗';
                    feedback.className = 'text-[10px] uppercase tracking-widest mt-2 font-bold text-red-500';
                }
            })
            .catch(() => {
                feedback.innerText = 'Erreur serveur ✗';
                feedback.className = 'text-[10px] uppercase tracking-widest mt-2 font-bold text-red-500';
            })
            .finally(() => btnVerify.disabled = false);
    });
</script>
@endsection