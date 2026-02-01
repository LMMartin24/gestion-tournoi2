<section class="bg-[#1a1a1a] border border-white/10 p-8 rounded-3xl">
    <header class="mb-8">
        <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">
            Profil <span class="text-indigo-500">Joueur</span>
        </h2>
        <p class="mt-1 text-xs text-gray-500 uppercase tracking-widest font-bold">
            // Mettez à jour vos informations personnelles et votre classement FFTT.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <x-input-label for="name" :value="__('Nom complet')" class="text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold mb-2" />
                <x-text-input id="name" name="name" type="text" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2 text-xs uppercase font-bold" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Adresse Email')" class="text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold mb-2" />
                <x-text-input id="email" name="email" type="email" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2 text-xs uppercase font-bold" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-gray-400 italic">
                            {{ __('Votre adresse email n\'est pas vérifiée.') }}
                            <button form="send-verification" class="underline text-indigo-500 hover:text-indigo-400 font-bold uppercase text-[10px] tracking-widest">
                                {{ __('Renvoyer le lien de vérification') }}
                            </button>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-white/5">
            <div>
                <x-input-label for="points" :value="__('Classement (Points)')" class="text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold mb-2" />
                <x-text-input id="points" name="points" type="number" class="block w-full bg-black/50 border-white/10 text-indigo-500 text-2xl font-black rounded-xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500" :value="old('points', $user->points)" required />
                <p class="text-[9px] text-gray-600 mt-2 uppercase italic tracking-tighter italic font-bold">Important pour l'éligibilité aux tableaux.</p>
                <x-input-error class="mt-2 text-xs uppercase font-bold" :messages="$errors->get('points')" />
            </div>

            <div>
                <x-input-label for="license_number" :value="__('Numéro de Licence')" class="text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold mb-2" />
                <x-text-input id="license_number" name="license_number" type="text" class="block w-full bg-black/50 border-white/10 text-white rounded-xl py-4 px-6 focus:ring-indigo-500 focus:border-indigo-500" :value="old('license_number', $user->license_number)" required />
                <x-input-error class="mt-2 text-xs uppercase font-bold" :messages="$errors->get('license_number')" />
            </div>
        </div>

        <div class="flex items-center gap-6 pt-6">
            <button type="submit" class="bg-indigo-600 text-white font-black uppercase text-xs tracking-[0.3em] px-10 py-4 rounded-full hover:bg-white hover:text-black transition-all duration-500 shadow-xl shadow-indigo-500/10 transform hover:-translate-y-1">
                {{ __('Sauvegarder les modifications') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-500 font-bold uppercase italic tracking-widest"
                >{{ __('Modifications enregistrées.') }}</p>
            @endif
        </div>
    </form>
</section>