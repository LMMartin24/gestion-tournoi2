<header class="absolute top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 py-6 flex justify-between items-center">
        <a href="/" class="text-white font-black tracking-tighter text-2xl flex items-center gap-2">
            <span class="bg-indigo-600 px-2 py-1 rounded text-sm">🏓</span>
            Amicale Pongiste de Ouistreham
        </a>

        <nav class="hidden md:flex items-center gap-8 text-[11px] uppercase tracking-[0.2em] font-bold text-white/90">
            <a href="#" class="hover:text-white transition">Infos Pratiques</a>
            <a href="#" class="hover:text-white transition">Choisir mon tableau</a>
            
            @if (Route::has('login'))
                <div class="flex items-center gap-6 ml-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-white text-black px-5 py-2 rounded-full hover:bg-indigo-500 hover:text-white transition">Mon Espace</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-white transition">Connexion</a>
                        <a href="{{ route('register') }}" class="border border-white/30 px-5 py-2 rounded-full hover:bg-white hover:text-black transition">Inscription</a>
                    @endauth
                </div>
            @endif
        </nav>
    </div>
</header>