@extends('layouts.app')

@section('content')

{{-- SECTION HERO DYNAMIQUE --}}
<section class="relative min-h-screen w-full flex items-center overflow-hidden bg-black">
    {{-- Background Image avec Overlay --}}
    <div class="absolute inset-0">
        @if($nextTournament && $nextTournament->image)
            <img src="{{ asset('storage/' . $nextTournament->image) }}" class="w-full h-full object-cover opacity-60" alt="{{ $nextTournament->name }}">
        @else
            <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-60" alt="Compétition Ping">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-[#1a1a1a] via-black/40 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-12 w-full py-20 md:py-32">
        <div class="max-w-5xl">
            {{-- Badge Édition --}}
            <div class="inline-block bg-indigo-600 text-white text-[10px] md:text-xs font-black uppercase tracking-[0.4em] px-6 py-2 mb-8 mt-10 md:mt-0">
                Édition {{ now()->year }} - {{ $nextTournament->location ?? 'France' }}
            </div>
            
            {{-- Titre Dynamique --}}
            <h1 class="text-white text-5xl md:text-8xl lg:text-[120px] font-[1000] uppercase italic leading-[1.1] md:leading-[0.8] tracking-tighter mb-10">
                @if($nextTournament)
                    A venir <br> 
                    <span class="text-indigo-500">{{ $nextTournament->name }}</span>
                @else
                    Prochainement <br> 
                    <span class="text-indigo-500">Nouveaux tournois</span>
                @endif
            </h1>
            
            <p class="mt-8 md:mt-12 text-gray-200 text-lg md:text-3xl max-w-2xl leading-tight font-medium">
                @if($nextTournament)
                    {{ $nextTournament->description ?? 'Prêt à monter au classement ? Rejoignez l\'élite du ping régional.' }}
                @else
                    La plateforme n°1 pour les inscriptions aux tournois de tennis de table.
                @endif
            </p>

            {{-- Boutons d'action --}}
            <div class="mt-10 md:mt-12 flex flex-col sm:flex-row gap-4 md:gap-6">
                @if($nextTournament)
                    <a href="/dashboard" class="text-center bg-white text-black font-[900] py-5 md:py-6 px-10 md:px-12 rounded-full uppercase tracking-[0.2em] text-xs md:text-sm hover:bg-indigo-500 hover:text-white transition-all shadow-2xl transform hover:scale-105">
                        Consulter les tableaux
                    </a>
                @endif
                
                @guest
                    <a href="{{ route('register') }}" class="text-center border-2 border-white/30 text-white font-[900] py-5 md:py-6 px-10 md:px-12 rounded-full uppercase tracking-[0.2em] text-xs md:text-sm hover:bg-white/10 transition-all">
                        Créer un compte coach
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="text-center border-2 border-white/30 text-white font-[900] py-5 md:py-6 px-10 md:px-12 rounded-full uppercase tracking-[0.2em] text-xs md:text-sm hover:bg-white/10 transition-all">
                        Mon Espace
                    </a>
                @endguest
            </div>

            {{-- Statistiques Dynamiques --}}
            <div class="mt-16 md:mt-24 grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 border-t border-white/10 pt-12">
                <div>
                    <p class="text-4xl md:text-6xl font-black text-white">
                        {{ $nextTournament ? $nextTournament->superTables->flatMap->subTables->count() : '0' }}
                    </p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Tableaux</p>
                </div>
                <div>
                    <p class="text-4xl md:text-6xl font-black text-white">
                        {{ $nextTournament ? $nextTournament->superTables->flatMap->subTables->flatMap->registrations->count() : $stats['total_registrations'] }}
                    </p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Inscriptions</p>
                </div>
                <div class="hidden sm:block">
                    <p class="text-4xl md:text-6xl font-black text-white">
                        {{ $stats['total_clubs'] }}
                    </p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Clubs Actifs</p>
                </div>
                <div class="hidden sm:block">
                    <p class="text-4xl md:text-6xl font-black text-white uppercase">FFTT</p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Homologué</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- SECTION INFOS --}}
<section id="features" class="bg-[#1a1a1a] py-20 md:py-40">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            {{-- Colonne Image & Date --}}
            <div class="order-1 lg:order-1">
                <div class="relative">
                    @if($nextTournament)
                        {{-- Badge Date MOBILE --}}
                        <div class="md:hidden bg-indigo-600 text-white p-6 mb-6 rounded-2xl flex justify-between items-center shadow-xl">
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.2em] font-black opacity-80 mb-1">Notre Prochain Évènement</p>
                                <p class="text-3xl font-[1000] italic leading-none uppercase">
                                    {{ \Carbon\Carbon::parse($nextTournament->date)->translatedFormat('d & d M') }}
                                </p>
                            </div>
                            <div class="bg-white/20 h-12 w-[1px]"></div>
                            <div class="text-right">
                                <p class="text-2xl font-black italic">APO</p>
                            </div>
                        </div>

                        <img src="{{ asset('images/elouan.jpg') }}" class="rounded-2xl shadow-2xl w-full object-cover" alt="Compétition">
                        
                        {{-- Bloc Date DESKTOP --}}
                        <div class="absolute -bottom-10 -left-10 bg-indigo-600 p-10 text-white hidden md:block shadow-2xl transform">
                            <p class="text-6xl font-[1000] italic leading-none uppercase">
                                {{ \Carbon\Carbon::parse($nextTournament->date)->translatedFormat('M') }}
                            </p>
                            <p class="text-xl uppercase tracking-[0.3em] font-black mt-2">
                                {{ \Carbon\Carbon::parse($nextTournament->date)->format('d & d') }} - {{ \Carbon\Carbon::parse($nextTournament->date)->format('Y') }}
                            </p>
                        </div>
                    @else
                         <img src="{{ asset('images/elouan.jpg') }}" class="rounded-2xl shadow-2xl w-full object-cover grayscale opacity-50" alt="Placeholder">
                    @endif
                </div>
            </div>
            
            {{-- Colonne Texte --}}
            <div class="text-white order-2 lg:order-2">
                <h2 class="text-4xl md:text-6xl font-[1000] mb-12 md:mb-16 leading-[1] uppercase italic tracking-tighter text-center lg:text-left">
                    Une gestion <br> <span class="text-indigo-400">100% digitale</span>
                </h2>

                <div class="divide-y-2 divide-white/5">
                    <div class="py-8 md:py-10 first:pt-0">
                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 italic items-center md:items-start text-center md:text-left">
                            <span class="text-4xl font-black text-indigo-500">01</span>
                            <div>
                                <h3 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">Vérification Automatique</h3>
                                <p class="text-gray-400 mt-4 text-base md:text-xl leading-snug font-medium">Notre algorithme vérifie les points FFTT des joueurs en temps réel pour garantir l'équité des tableaux.</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-8 md:py-10">
                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 italic items-center md:items-start text-center md:text-left">
                            <span class="text-4xl font-black text-indigo-500">02</span>
                            <div>
                                <h3 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">Espace Club Dédié</h3>
                                <p class="text-gray-400 mt-4 text-base md:text-xl leading-snug font-medium">En tant que coach, gérez tous vos joueurs depuis un tableau de bord unique. Inscriptions groupées en 30 secondes.</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-8 md:py-10">
                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 italic items-center md:items-start text-center md:text-left">
                            <span class="text-4xl font-black text-indigo-500">03</span>
                            <div>
                                <h3 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">Export GIRPE Direct</h3>
                                <p class="text-gray-400 mt-4 text-base md:text-xl leading-snug font-medium">Organisateurs, récupérez vos fichiers d'inscriptions prêts à être importés dans GIRPE. Zéro saisie manuelle.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- SECTION AUTRES TOURNOIS (CALENDRIER) --}}
@if($upcomingTournaments->count() > 0)
<section class="bg-black py-20 border-t border-white/5">
    <div class="max-w-7xl mx-auto px-6">
        <h3 class="text-2xl font-black text-white uppercase italic mb-10 tracking-widest">
            <span class="text-indigo-500">//</span> Calendrier à venir
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($upcomingTournaments as $tournament)
                <a href="{{ route('tournaments.public.show', $tournament->slug) }}" class="group bg-[#111] border border-white/5 p-8 rounded-[2rem] hover:bg-indigo-600 transition-all duration-500">
                    <p class="text-gray-500 group-hover:text-white/60 text-[10px] font-black uppercase mb-2">
                        {{ \Carbon\Carbon::parse($tournament->date)->translatedFormat('d F Y') }}
                    </p>
                    <h4 class="text-white text-xl font-black uppercase italic group-hover:scale-105 transition-transform">{{ $tournament->name }}</h4>
                    <p class="text-indigo-500 group-hover:text-white text-[10px] font-black uppercase mt-4 tracking-widest">Voir les détails →</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection