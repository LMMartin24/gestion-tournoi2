<header class="absolute top-0 left-0 w-full z-50">
    <div class="max-w-[3200px] mx-auto px-8 lg:px-16 py-10 flex justify-between items-center">
        
        <a href="/" class="text-white font-[1000] tracking-tighter text-3xl md:text-4xl flex items-center gap-4 uppercase italic">
            <span class="bg-indigo-600 w-12 h-12 flex items-center justify-center rounded-xl shadow-lg not-italic text-2xl">🏓</span>
            <span class="leading-none">Amicale Pongiste <br class="hidden sm:block"> <span class="text-indigo-500">Ouistreham</span></span>
        </a>

        <nav class="hidden xl:flex items-center gap-12 text-xs uppercase tracking-[0.3em] font-[900] text-white">
            <a href="#" class="text-xl hover:text-indigo-400 transition-colors py-2 border-b-2 border-transparent hover:border-indigo-400">Infos Pratiques</a>
            <a href="{{route('dashboard')}}" class="text-xl hover:text-indigo-400 transition-colors py-2 border-b-2 border-transparent hover:border-indigo-400">Choisir mon tableau</a>

            @if (Route::has('login'))
                <div class="flex items-center gap-8 ml-6">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-xl bg-white text-black px-10 py-4 rounded-full hover:bg-indigo-600 hover:text-white transition-all shadow-xl transform hover:scale-105">
                            Mon Espace
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    title="Se déconnecter"
                                    class="p-2.5 text-gray-100 hover:text-red-500 hover:bg-red-500/10 border border-white/5 hover:border-red-500/20 rounded-2xl transition-all duration-300 group">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-xl hover:text-indigo-400 transition-colors">Connexion</a>
                        <a href="{{ route('register') }}" class="text-xl border-2 border-white/30 px-10 py-4 rounded-full hover:bg-white hover:text-black transition-all font-black tracking-[0.2em]">
                            Inscription
                        </a>
                    @endauth
                </div>
            @endif
        </nav>

        <div class="xl:hidden">
            <button class="text-white p-2">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>
    </div>
</header>