@extends('layouts.app')

@section('content')

<section class="relative h-screen w-full flex items-center overflow-hidden bg-black">
    <div class="absolute inset-0">
        <img src="{{ asset('images/salle2.jpg') }}" class="w-full h-full object-cover opacity-60" alt="Compétition Ping">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-12 w-full">
        <div class="max-w-4xl">
            <div class="inline-block bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-[0.3em] px-4 py-1 mb-6">
                Édition 2026 - Ouistreham
            </div>
            <h1 class="text-white text-6xl md:text-8xl font-black uppercase italic leading-[0.85] tracking-tighter">
                A venir <br> 
                <span class="text-indigo-500">Le tournoi de Ouistreham</span>
            </h1>
            
            <p class="mt-8 text-gray-300 text-lg md:text-xl max-w-xl leading-relaxed">
                Le grand tournoi annuel de Ouistreham revient. 24 tables, 8 tableaux, et plus de 2000€ de dotations. Prêt à monter au classement ?
            </p>

            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('register') }}" class="bg-white text-black font-extrabold py-5 px-10 rounded-full uppercase tracking-widest text-xs hover:bg-indigo-500 hover:text-white transition-all shadow-xl">
                    S'inscrire au tournoi
                </a>
                <a href="#tableaux" class="border border-white/30 text-white font-extrabold py-5 px-10 rounded-full uppercase tracking-widest text-xs hover:bg-white/10 transition-all">
                    Voir les tableaux
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-4xl font-black text-black">24</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Tables Butterfly</p>
            </div>
            <div>
                <p class="text-4xl font-black text-black">8</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Tableaux</p>
            </div>
            <div>
                <p class="text-4xl font-black text-black">2000€</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">De Dotations</p>
            </div>
            <div>
                <p class="text-4xl font-black text-black">FFTT</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold">Tournoi Homologué</p>
            </div>
        </div>
    </div>
</section>

<section id="tableaux" class="bg-[#420337] py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            
            <div class="relative">
                <img src="{{ asset('images/elouan.jpg') }}" class="rounded-lg shadow-2xl w-full h-[600px] object-cover" alt="Trophée tournoi">
                <div class="absolute -bottom-6 -right-6 bg-indigo-600 p-8 text-white hidden md:block">
                    <p class="text-4xl font-black italic">MARS</p>
                    <p class="text-sm uppercase tracking-widest font-bold">14 & 15 - 2026</p>
                </div>
            </div>

            <div class="text-white">
                <h2 class="text-5xl font-extrabold mb-12 leading-tight">Une organisation <br> pensée pour les compétiteurs</h2>

                <div class="divide-y divide-white/10">
                    <div class="py-6 first:pt-0">
                        <div class="flex gap-6 italic">
                            <span class="text-2xl font-black text-indigo-400">01</span>
                            <div>
                                <h3 class="text-lg font-bold uppercase tracking-wide">Vérification Instantanée</h3>
                                <p class="text-gray-400 mt-2 text-sm leading-relaxed">Grâce à notre connexion API FFTT, votre licence et vos points sont vérifiés en 1 clic. Pas de paperasse inutile.</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-6">
                        <div class="flex gap-6 italic">
                            <span class="text-2xl font-black text-indigo-400">02</span>
                            <div>
                                <h3 class="text-lg font-bold uppercase tracking-wide">Tableaux par Classement</h3>
                                <p class="text-gray-400 mt-2 text-sm leading-relaxed">De NC à Numérotés. Des poules de 3 ou 4 joueurs pour vous garantir un maximum de matchs durant le week-end.</p>
                            </div>
                        </div>
                    </div>

                    <div class="py-6">
                        <div class="flex gap-6 italic">
                            <span class="text-2xl font-black text-indigo-400">03</span>
                            <div>
                                <h3 class="text-lg font-bold uppercase tracking-wide">Espace Restauration & Pro-Shop</h3>
                                <p class="text-gray-400 mt-2 text-sm leading-relaxed">Buvette complète, repas chauds et stand de matériel sur place pour répondre à tous vos besoins.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-white/5 border border-white/10 rounded-lg">
                    <p class="text-sm text-gray-300 italic">"Le meilleur tournoi de la région pour lancer sa deuxième phase de championnat."</p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection