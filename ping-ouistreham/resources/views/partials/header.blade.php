<header x-data="{ open: false }" class=" top-0 left-0 w-full z-50 bg-[#1a1a1a]">
    <div class="max-w-[3200px] mx-auto px-8 lg:px-16 py-10 flex justify-between items-center">
        
        {{-- LOGO --}}
        <a href="/" class="text-white font-[1000] tracking-tighter text-3xl md:text-4xl flex items-center gap-4 uppercase italic">
            <span class="bg-indigo-600 w-12 h-12 flex items-center justify-center rounded-xl shadow-lg not-italic text-2xl">🏓</span>
            <span class="leading-none">AP <span class="text-indigo-500">Ouistreham</span></span>
        </a>

        {{-- DESKTOP NAV --}}
        <nav class="hidden xl:flex items-center gap-12 text-xs uppercase tracking-[0.3em] font-[900] text-white">
            <a href="{{route('dashboard')}}" class="text-xl hover:text-indigo-400 transition-colors py-2 border-b-2 border-transparent hover:border-indigo-400">Choisir mon tableau</a>

            @if (Route::has('login'))
                <div class="flex items-center gap-8 ml-6">
                    @auth
                        {{-- BOUTON ADMIN (Visible seulement si admin) --}}
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.tournaments.index') }}" class="text-sm bg-red-600 text-white px-6 py-3 rounded-full hover:bg-white hover:text-red-600 transition-all">Admin</a>
                        @endif

                        <a href="{{ url('/dashboard') }}" class="text-xl bg-white text-black px-10 py-4 rounded-full hover:bg-indigo-600 hover:text-white transition-all shadow-xl">
                            Mon Espace
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="p-2.5 text-gray-100 hover:text-red-500 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-xl hover:text-indigo-400 transition-colors">Connexion</a>
                        <a href="{{ route('register') }}" class="text-xl border-2 border-white/30 px-10 py-4 rounded-full hover:bg-white hover:text-black transition-all font-black tracking-[0.2em]">Inscription</a>
                    @endauth
                </div>
            @endif
        </nav>

        {{-- MENU ADMINISTRATION --}}
@if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin'))
    <div class="relative group">
        <button class="flex items-center gap-2 px-4 py-2 text-xs font-black uppercase tracking-widest text-indigo-400 border border-indigo-500/20 rounded-xl bg-indigo-500/5 hover:bg-indigo-500 hover:text-white transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Admin
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Dropdown au survol --}}
        <div class="absolute left-0 mt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
            <div class="bg-[#111] border border-white/10 rounded-2xl shadow-2xl p-2 backdrop-blur-xl">
                <a href="{{ route('admin.tournaments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors text-[10px] font-black uppercase tracking-widest text-gray-300 hover:text-indigo-400">
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                    Gérer les Tournois
                </a>
                <a href="{{ route('admin.tournaments.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors text-[10px] font-black uppercase tracking-widest text-gray-300 hover:text-green-500">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                    Nouveau Tournoi
                </a>
                <div class="h-px bg-white/5 my-2"></div>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-colors text-[10px] font-black uppercase tracking-widest text-gray-500 italic">
                    <span class="w-1.5 h-1.5 bg-gray-700 rounded-full"></span>
                    Utilisateurs (Bientôt)
                </a>
            </div>
        </div>
    </div>
@endif

        {{-- MOBILE BURGER BUTTON --}}
        <div class="xl:hidden">
            <button @click="open = !open" class="text-white  relative z-[100]">
                <svg x-show="!open" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <svg x-show="open" x-cloak class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- MOBILE MENU OVERLAY --}}
        <div x-show="open" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 bg-black z-[90] flex flex-col items-center justify-center p-8 xl:hidden">
            
            <nav class="flex flex-col items-center gap-8 text-center">
                <a href="{{route('dashboard')}}" @click="open = false" class="text-3xl font-black uppercase italic text-white hover:text-indigo-500 transition-colors">Choisir mon tableau</a>
                
                @auth
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.tournaments.index') }}" @click="open = false" class="text-2xl font-black uppercase text-red-500">Administration</a>
                    @endif
                    
                    <a href="{{ url('/dashboard') }}" @click="open = false" class="text-4xl font-black uppercase italic bg-white text-black px-12 py-6 rounded-full">Mon Espace</a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-2xl font-black uppercase text-gray-500 hover:text-red-500 transition-all">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" @click="open = false" class="text-3xl font-black uppercase text-white">Connexion</a>
                    <a href="{{ route('register') }}" @click="open = false" class="text-3xl font-black uppercase italic bg-indigo-600 text-white px-12 py-6 rounded-full">Inscription</a>
                @endauth
            </nav>
        </div>
    </div>
</header>