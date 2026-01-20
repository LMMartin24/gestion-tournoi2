@extends('layouts.app')

@section('content')

<section class="relative h-screen w-full flex items-center overflow-hidden bg-black">
    <div class="absolute inset-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-60" alt="Compétition Ping">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-12 w-full">
        <div class="max-w-5xl">
            <div class="inline-block bg-indigo-600 text-white text-xs font-black uppercase tracking-[0.4em] px-6 py-2 mt-64 mb-8">
                Édition 2026 - Ouistreham
            </div>
            
            <h1 class="text-white text-7xl md:text-[120px] font-[1000] uppercase italic leading-[0.8] tracking-tighter mb-10">
                A venir <br> 
                <span class="text-indigo-500">Le tournoi de Ouistreham</span>
            </h1>
            
            <p class="mt-12 text-gray-200 text-xl md:text-3xl max-w-2xl leading-tight font-medium">
                24 tables, 8 tableaux, et plus de 2000€ de dotations. Prêt à monter au classement ?
            </p>

            <div class="mt-12 flex flex-wrap gap-6">
                <a href="{{ route('register') }}" class="bg-white text-black font-[900] py-6 px-12 rounded-full uppercase tracking-[0.2em] text-sm hover:bg-indigo-500 hover:text-white transition-all shadow-2xl transform hover:scale-105">
                    S'inscrire au tournoi
                </a>
                <a href="#tableaux" class="border-2 border-white/30 text-white font-[900] py-6 px-12 rounded-full uppercase tracking-[0.2em] text-sm hover:bg-white/10 transition-all">
                    Voir les tableaux
                </a>
            </div>

            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-12 border-t border-white/10 pt-12 mb-20">
                <div>
                    <p class="text-5xl md:text-6xl font-black text-white">24</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Tables Butterfly</p>
                </div>
                <div>
                    <p class="text-5xl md:text-6xl font-black text-white">8</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Tableaux</p>
                </div>
                <div>
                    <p class="text-5xl md:text-6xl font-black text-white">2000€</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Dotations</p>
                </div>
                <div>
                    <p class="text-5xl md:text-6xl font-black text-white uppercase">FFTT</p>
                    <p class="text-xs uppercase tracking-[0.3em] text-indigo-400 font-black mt-2">Homologué</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="tableaux" class="bg-[#420337] py-32">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
            
            <div class="relative">
                <img src="{{ asset('images/elouan.jpg') }}" class="rounded-2xl shadow-2xl w-full h-[700px] object-cover" alt="Trophée tournoi">
                <div class="absolute -bottom-10 -right-10 bg-indigo-600 p-12 text-white hidden md:block shadow-2xl transform">
                    <p class="text-6xl font-[1000] italic leading-none">MARS</p>
                    <p class="text-xl uppercase tracking-[0.3em] font-black mt-2">14 & 15 - 2026</p>
                </div>
            </div>

            <div class="text-white">
                <h2 class="text-6xl md:text-5xl font-[1000] mb-16 leading-[0.9] uppercase italic tracking-tighter">
                    Une organisation <br> <span class="text-indigo-400">sans compromis</span>
                </h2>

                <div class="divide-y-2 divide-white/5">
                    <div class="py-10 first:pt-0">
                        <div class="flex gap-8 italic">
                            <span class="text-4xl font-black text-indigo-500">01</span>
                            <div>
                                <h3 class="text-2xl font-black uppercase tracking-wider">Vérification Instantanée</h3>
                                <p class="text-gray-300 mt-4 text-lg md:text-xl leading-snug font-medium">Grâce à notre connexion API FFTT, votre licence et vos points sont vérifiés en 1 clic. Pas de paperasse.</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-10">
                        <div class="flex gap-8 italic">
                            <span class="text-4xl font-black text-indigo-500">02</span>
                            <div>
                                <h3 class="text-2xl font-black uppercase tracking-wider">Tableaux par Classement</h3>
                                <p class="text-gray-300 mt-4 text-lg md:text-xl leading-snug font-medium">De NC à Numérotés. Des poules de 3 ou 4 joueurs pour vous garantir un maximum de matchs.</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-10">
                        <div class="flex gap-8 italic">
                            <span class="text-4xl font-black text-indigo-500">03</span>
                            <div>
                                <h3 class="text-2xl font-black uppercase tracking-wider">Espace Restauration</h3>
                                <p class="text-gray-300 mt-4 text-lg md:text-xl leading-snug font-medium">Buvette complète, repas chauds et stand de matériel Pro-Shop sur place.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-16 p-10 bg-white/5 border-l-4 border-indigo-500 rounded-r-xl">
                    <p class="text-xl md:text-2xl text-gray-200 italic font-medium leading-relaxed">
                        "Le meilleur tournoi de la région pour lancer sa deuxième phase de championnat."
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection