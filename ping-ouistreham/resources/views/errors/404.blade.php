@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black relative flex items-center justify-center overflow-hidden">
    
    <div class="absolute inset-0 z-0">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[800px] h-[300px] bg-indigo-600/10 blur-[120px] rounded-full rotate-12"></div>
    </div>

    <div class="relative z-10 w-full max-w-2xl px-6 flex flex-col items-center text-center">
        
        <div class="relative mb-8">
            <span class="absolute inset-0 -top-16 left-1/2 -translate-x-1/2 text-[10rem] md:text-[15rem] font-black text-white/[0.03] select-none z-0">404</span>
            <h1 class="relative z-10 text-6xl md:text-8xl font-black text-white uppercase italic tracking-tighter leading-none">
                Balle <br>
                <span class="text-indigo-500">Hors Limites</span>
            </h1>
        </div>

        <div class="space-y-4 mb-12">
            <p class="text-gray-400 text-lg md:text-xl font-medium max-w-md mx-auto leading-relaxed">
                Désolé, la page que vous cherchez est tombée <span class="text-white">"sur la carre"</span> ou n'existe plus.
            </p>
            <p class="text-indigo-500 uppercase tracking-[0.3em] text-xs font-bold">
                // L'arbitre a tranché : point perdu
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
            <a href="{{ url('/') }}" 
               class="px-10 py-5 bg-white text-black font-black uppercase text-xs tracking-[0.2em] rounded-full hover:bg-indigo-500 hover:text-white transition-all duration-300 shadow-xl">
                Retour à l'accueil
            </a>
            
            <a href="javascript:history.back()" 
               class="px-10 py-5 bg-transparent border border-white/20 text-white font-black uppercase text-xs tracking-[0.2em] rounded-full hover:bg-white/10 transition-all duration-300">
                Service précédent
            </a>
        </div>
    </div>

    <div class="absolute bottom-10 left-10 hidden lg:block">
        <p class="text-gray-800 text-[10px] uppercase tracking-[0.5em] font-bold rotate-90 origin-left">
            Error Code 404 // PING OUISTREHAM
        </p>
    </div>
</div>
@endsection