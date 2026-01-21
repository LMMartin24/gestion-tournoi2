@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black relative flex items-center">
    
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-20 grayscale">
        <div class="absolute inset-0 bg-gradient-to-b from-black via-transparent to-black"></div>
    </div>

    <div class="relative z-10 max-w-2xl mx-auto px-6 w-full">
        <div class="bg-[#1a1a1a] border border-white/10 p-10 md:p-16 rounded-3xl shadow-2xl">
            
            <div class="mb-12">
                <h2 class="text-4xl md:text-5xl font-black text-white uppercase italic tracking-tighter">
                    Connexion <span class="text-indigo-500 text-sm md:text-base not-italic ml-2 tracking-widest">// ACCÈS COMPÉTITEUR</span>
                </h2>
                <p class="text-gray-200 text-xs uppercase tracking-[0.3em] mt-4 font-bold">Heureux de vous revoir parmi nous</p>
            </div>

            <x-auth-session-status class="mb-6 text-green-500 text-xs font-bold uppercase tracking-widest" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-white mb-3 uppercase text-xs tracking-widest font-bold" />
                    <x-text-input id="email" 
                        class="block w-full bg-black/50 border-white/10 text-white placeholder:text-white rounded-2xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                        type="email" name="email" :value="old('email')" 
                        required autofocus autocomplete="username" 
                        placeholder="votre@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500 font-bold uppercase tracking-tight" />
                </div>

                <div>
                    <div class="flex justify-between items-center mb-3">
                        <x-input-label for="password" :value="__('Mot de passe')" class="text-white uppercase text-xs tracking-widest font-bold" />
                        @if (Route::has('password.request'))
                            <a class="text-[10px] md:text-xs uppercase tracking-widest text-indigo-400 hover:text-white transition-all font-bold" href="{{ route('password.request') }}">
                                {{ __('Oublié ?') }}
                            </a>
                        @endif
                    </div>
                    <x-text-input id="password" 
                        class="block w-full bg-black/50 border-white/10 text-white placeholder:text-white rounded-2xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                        type="password"
                        name="password"
                        required autocomplete="current-password" 
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500 font-bold uppercase tracking-tight" />
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full hover:bg-white hover:text-black font-black uppercase text-xs md:text-sm tracking-[0.3em] py-5 px-10 rounded-full bg-indigo-500 text-white transition-all duration-300 shadow-xl transform hover:-translate-y-1">
                        {{ __('Se connecter au tableau de bord') }}
                    </button>
                </div>
            </form>

            <div class="mt-12 pt-8 border-t border-white/5 text-center">
                <p class="text-xs uppercase tracking-widest text-gray-500">
                    Nouveau sur le tournoi ? 
                    <a href="{{ route('register') }}" class="text-white font-black hover:text-indigo-400 ml-2 transition-colors border-b border-indigo-500/50 pb-1">
                        Créer un profil joueur
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection