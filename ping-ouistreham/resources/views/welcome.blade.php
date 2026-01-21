@extends('layouts.app')

@section('content')

{{-- SECTION HERO --}}
<section class="relative min-h-screen w-full flex items-center overflow-hidden bg-black">
    {{-- Background Image avec Overlay --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-60" alt="Compétition Ping">
        <div class="absolute inset-0 bg-gradient-to-t from-[#1a1a1a] via-black/40 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-12 w-full py-20 md:py-32">
        <div class="max-w-5xl">
            {{-- Badge Édition --}}
            <div class="inline-block bg-indigo-600 text-white text-[10px] md:text-xs font-black uppercase tracking-[0.4em] px-6 py-2 mb-8 mt-10 md:mt-0">
                Édition 2026 - Ouistreham
            </div>
            
            {{-- Titre --}}
            <h1 class="text-white text-5xl md:text-8xl lg:text-[120px] font-[1000] uppercase italic leading-[1.1] md:leading-[0.8] tracking-tighter mb-10">
                A venir <br> 
                <span class="text-indigo-500">Le tournoi de Ouistreham</span>
            </h1>
            
            <p class="mt-8 md:mt-12 text-gray-200 text-lg md:text-3xl max-w-2xl leading-tight font-medium">
                24 tables, 8 tableaux, et plus de 2000€ de dotations. Prêt à monter au classement ?
            </p>

            {{-- Boutons d'action --}}
            <div class="mt-10 md:mt-12 flex flex-col sm:flex-row gap-4 md:gap-6">
                <a href="{{ route('register') }}" class="text-center bg-white text-black font-[900] py-5 md:py-6 px-10 md:px-12 rounded-full uppercase tracking-[0.2em] text-xs md:text-sm hover:bg-indigo-500 hover:text-white transition-all shadow-2xl transform hover:scale-105">
                    S'inscrire au tournoi
                </a>
                <a href="{{ asset('pdfs/reglement.pdf') }}" download="Reglement_Tournoi.pdf" class="text-center border-2 border-white/30 text-white font-[900] py-5 md:py-6 px-10 md:px-12 rounded-full uppercase tracking-[0.2em] text-xs md:text-sm hover:bg-white/10 transition-all">
                    Règlement PDF
                </a>
            </div>

            {{-- Statistiques --}}
            <div class="mt-16 md:mt-24 grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 border-t border-white/10 pt-12">
                <div>
                    <p class="text-4xl md:text-6xl font-black text-white">24</p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Tables Butterfly</p>
                </div>
                <div>
                    <p class="text-4xl md:text-6xl font-black text-white">8</p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Tableaux</p>
                </div>
                {{-- Stats secondaires (cachées sur petit mobile) --}}
                <div class="hidden sm:block">
                    <p class="text-4xl md:text-6xl font-black text-white">2000€</p>
                    <p class="text-[10px] uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Dotations</p>
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
<section id="tableaux" class="bg-[#1a1a1a] py-20 md:py-40">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            
            {{-- Colonne Image & Date --}}
            <div class="order-1 lg:order-1">
                <div class="relative">
                    {{-- Badge Date MOBILE --}}
                    <div class="md:hidden bg-indigo-600 text-white p-6 mb-6 rounded-2xl flex justify-between items-center shadow-xl">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.2em] font-black opacity-80 mb-1">Dates 2026</p>
                            <p class="text-3xl font-[1000] italic leading-none uppercase">14 & 15 Mars</p>
                        </div>
                        <div class="bg-white/20 h-12 w-[1px]"></div>
                        <div class="text-right">
                            <p class="text-2xl font-black italic">APO</p>
                        </div>
                    </div>

                    <img src="{{ asset('images/elouan.jpg') }}" class="rounded-2xl shadow-2xl w-full object-cover" alt="Trophée tournoi">
                    
                    {{-- Bloc Date DESKTOP --}}
                    <div class="absolute -bottom-10 -left-10 bg-indigo-600 p-10 text-white hidden md:block shadow-2xl transform">
                        <p class="text-6xl font-[1000] italic leading-none">MARS</p>
                        <p class="text-xl uppercase tracking-[0.3em] font-black mt-2">14 & 15 - 2026</p>
                    </div>
                </div>
                
                <div class="mt-12 md:mt-20 p-8 md:p-10 bg-white/5 border-l-4 border-indigo-500 rounded-r-xl">
                    <p class="text-lg md:text-2xl text-gray-200 italic font-medium leading-relaxed">
                        "Le meilleur tournoi de la région pour lancer sa deuxième phase de championnat."
                    </p>
                </div>
            </div>
            
            {{-- Colonne Texte --}}
            <div class="text-white order-2 lg:order-2">
                <h2 class="text-4xl md:text-6xl font-[1000] mb-12 md:mb-16 leading-[1] uppercase italic tracking-tighter text-center lg:text-left">
                    Une organisation <br> <span class="text-indigo-400">sans compromis</span>
                </h2>

                <div class="divide-y-2 divide-white/5">
                    {{-- Point 01 --}}
                    <div class="py-8 md:py-10 first:pt-0">
                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 italic items-center md:items-start text-center md:text-left">
                            <span class="text-4xl font-black text-indigo-500">01</span>
                            <div>
                                <h3 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">Vérification Instantanée</h3>
                                <p class="text-gray-400 mt-4 text-base md:text-xl leading-snug font-medium">Grâce à notre connexion API FFTT, votre licence et vos points sont vérifiés en 1 clic.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Point 02 --}}
                    <div class="py-8 md:py-10">
                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 italic items-center md:items-start text-center md:text-left">
                            <span class="text-4xl font-black text-indigo-500">02</span>
                            <div>
                                <h3 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">Tableaux par Classement</h3>
                                <p class="text-gray-400 mt-4 text-base md:text-xl leading-snug font-medium">De NC à Numérotés. Des poules de 3 ou 4 joueurs pour vous garantir un maximum de matchs.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Point 03 --}}
                    <div class="py-8 md:py-10">
                        <div class="flex flex-col md:flex-row gap-6 md:gap-8 italic items-center md:items-start text-center md:text-left">
                            <span class="text-4xl font-black text-indigo-500">03</span>
                            <div>
                                <h3 class="text-xl md:text-2xl font-black uppercase tracking-wider text-white">Espace Restauration</h3>
                                <p class="text-gray-400 mt-4 text-base md:text-xl leading-snug font-medium">Buvette complète, repas chauds et stand de matériel Pro-Shop sur place.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection