@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#0a0a0a] text-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        
        {{-- AFFICHAGE DES ERREURS ET SUCCÈS --}}
        @if ($errors->any() || session('success') || session('error'))
            <div class="mb-10 space-y-4">
                @if(session('success'))
                    <div class="bg-green-500/20 border border-green-500/50 text-green-500 p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                        // SUCCESS: {{ session('success') }}
                    </div>
                @endif
                @if(session('error') || $errors->any())
                    <div class="bg-red-500/20 border border-red-500/50 text-red-500 p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">
                        // ERROR: {{ session('error') ?? $errors->first() }}
                    </div>
                @endif
            </div>
        @endif

        {{-- HEADER DU DASHBOARD --}}
        <div class="mb-12">
            <h1 class="text-4xl md:text-6xl font-[1000] uppercase italic tracking-tighter">
                Mon Espace <span class="text-indigo-500">Joueur</span>
            </h1>
            <div class="flex items-center gap-4 mt-2">
                <p class="text-gray-400 uppercase tracking-widest text-sm font-bold">
                    {{ Auth::user()->name }} — {{ Auth::user()->points }} pts
                </p>
                <span class="h-px w-12 bg-white/10"></span>
                <span class="text-indigo-400 text-xs font-black uppercase tracking-widest">Membre Officiel</span>
            </div>
        </div>

        {{-- SECTION 1 : MES ENGAGEMENTS --}}
        <div class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-xl font-black uppercase italic tracking-tighter flex items-center gap-3">
                    <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                    Mes Engagements <span class="text-gray-600 text-sm ml-2">({{ $myRegistrations->count() }})</span>
                </h2>
            </div>

            @if($myRegistrations->isEmpty())
                <div class="bg-[#111] border border-white/5 rounded-3xl p-12 text-center">
                    <p class="text-gray-500 font-medium italic">Tu n'es inscrit à aucun tableau pour le moment.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($myRegistrations as $registration)
                        @php $isLocked = $registration->subTable->superTable->is_locked; @endphp
                        <div class="group bg-[#111] border {{ $isLocked ? 'border-amber-500/20' : 'border-white/5' }} p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between hover:border-indigo-500/50 transition-all duration-300 relative overflow-hidden">
                            
                            @if($isLocked)
                                <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
                            @endif

                            <div class="flex items-center gap-6">
                                <div class="hidden sm:flex flex-col items-center justify-center bg-black/40 border border-white/5 px-4 py-2 rounded-xl min-w-[100px]">
                                    <span class="text-[8px] text-gray-500 font-black uppercase tracking-tighter">Série</span>
                                    <span class="text-xs font-bold text-indigo-400 uppercase text-center">{{ $registration->subTable->superTable->name }}</span>
                                </div>

                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <h3 class="text-2xl font-[1000] uppercase italic tracking-tighter group-hover:text-indigo-400 transition-colors">
                                            {{ $registration->subTable->label }}
                                        </h3>
                                        
                                        @if($registration->status === 'confirmed')
                                            <span class="bg-green-500/10 text-green-500 text-[9px] font-black uppercase px-2 py-0.5 rounded-md border border-green-500/20">Confirmé</span>
                                        @else
                                            <span class="bg-orange-500/10 text-orange-500 text-[9px] font-black uppercase px-2 py-0.5 rounded-md border border-orange-500/20">En attente</span>
                                        @endif

                                        @if($isLocked)
                                            <span class="bg-amber-500/10 text-amber-500 text-[9px] font-black uppercase px-2 py-0.5 rounded-md border border-amber-500/20 italic">Verrouillé</span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-gray-500 text-[10px] uppercase font-bold tracking-widest">
                                        <span class="text-white/60">{{ $registration->subTable->superTable->tournament->name }}</span>
                                        <span class="text-gray-800">/</span>
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-clock"></i>
                                            Début : {{ \Carbon\Carbon::parse($registration->subTable->superTable->start_time)->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 md:mt-0 flex items-center justify-between md:justify-end gap-8">
                                <div class="text-right">
                                    <p class="text-gray-500 text-[9px] font-black uppercase tracking-widest leading-none">Frais</p>
                                    <p class="text-2xl font-black text-white italic">{{ number_format($registration->subTable->entry_fee, 2) }}€</p>
                                </div>
                                
                                {{-- Désinscription possible uniquement si non verrouillé --}}
                                @if(!$isLocked)
                                    <form action="{{ route('player.unregister', $registration->subTable->id) }}" method="POST" onsubmit="return confirm('Désinscription ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-white/5 hover:bg-red-600 hover:text-white text-gray-500 p-4 rounded-xl transition-all border border-white/5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <div class="bg-amber-500/10 p-4 rounded-xl border border-amber-500/20 text-amber-500 cursor-help" title="Inscriptions fermées par l'organisateur">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 bg-indigo-600 p-6 rounded-2xl flex justify-between items-center shadow-2xl shadow-indigo-600/20 border border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white/10 rounded-xl">
                            <i class="fas fa-wallet text-white text-xl"></i>
                        </div>
                        <span class="font-black uppercase italic tracking-tighter text-lg text-white">Total à régler sur place</span>
                    </div>
                    <span class="text-4xl font-[1000] italic text-white">{{ number_format($totalToPay, 2) }}€</span>
                </div>
            @endif
        </div>

        {{-- SECTION 2 : DISPONIBLES --}}
        <div>
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-xl font-black uppercase italic tracking-tighter flex items-center gap-3">
                        <span class="w-2 h-6 bg-green-500 rounded-full"></span>
                        Tableaux Disponibles
                    </h2>
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mt-2">
                        Règle du tournoi : <span class="text-indigo-400">Maximum 2 tableaux par joueur</span>
                    </p>
                </div>

                @if($myRegistrations->count() >= 2)
                    <div class="bg-orange-500/10 border border-orange-500/20 px-4 py-2 rounded-lg flex items-center gap-3">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                        </span>
                        <p class="text-[10px] font-black uppercase text-orange-500 tracking-tighter">
                            Tu as atteint ta limite
                        </p>
                    </div>
                @endif
            </div>

            @if($availableSubTables->isEmpty())
                <div class="bg-[#111] border border-white/5 rounded-3xl p-12 text-center text-gray-500 italic">
                    Aucun nouveau tableau disponible selon ton classement.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableSubTables as $subTable)
                        @php
                            $max = (int) $subTable->superTable->max_players;
                            $current = $subTable->superTable->registrations->where('status', 'confirmed')->count();
                            $isFull = $current >= $max;
                            $isLocked = $subTable->superTable->is_locked;
                            $percentage = $max > 0 ? round(($current / $max) * 100) : 0;
                        @endphp

                        <div class="bg-[#111] border {{ $isLocked ? 'border-amber-500/30' : ($isFull ? 'border-red-500/30' : 'border-white/5') }} p-8 rounded-[2rem] hover:border-indigo-500/50 transition-all flex flex-col justify-between group relative overflow-hidden">
                            
                            {{-- Rubans d'état --}}
                            @if($isLocked)
                                <div class="absolute top-3 right-[-30px] bg-amber-500 text-black text-[7px] font-black py-1 px-8 transform rotate-45 uppercase tracking-tighter shadow-xl">
                                    BLOQUÉ
                                </div>
                            @elseif($isFull)
                                <div class="absolute top-3 right-[-30px] bg-red-600 text-white text-[7px] font-black py-1 px-8 transform rotate-45 uppercase tracking-tighter shadow-xl">
                                    COMPLET
                                </div>
                            @endif

                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-3xl font-[1000] uppercase italic leading-none group-hover:text-indigo-400 transition-colors {{ $isLocked ? 'opacity-50' : '' }}">
                                        {{ $subTable->label }}
                                    </h3>
                                    <span class="{{ $isLocked ? 'text-amber-500' : ($isFull ? 'text-red-500' : 'text-green-500') }} font-black italic text-lg">{{ number_format($subTable->entry_fee, 2) }}€</span>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-indigo-500 text-[10px] font-black uppercase tracking-[0.2em]">{{ $subTable->superTable->tournament->name }}</p>
                                </div>

                                {{-- Barre de remplissage --}}
                                <div class="mb-6">
                                    <div class="flex justify-between text-[8px] font-black uppercase mb-1 tracking-widest">
                                        <span class="{{ $isLocked ? 'text-amber-500' : ($isFull ? 'text-red-500' : 'text-gray-500') }}">
                                            @if($isLocked) INSCRIPTIONS FERMÉES @elseif($isFull) TABLEAU PLEIN @else Places disponibles @endif
                                        </span>
                                        <span class="text-white">{{ $current }} / {{ $max }}</span>
                                    </div>
                                    <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                                        <div class="h-full {{ $isLocked ? 'bg-amber-500' : ($isFull ? 'bg-red-600' : 'bg-indigo-600') }} transition-all duration-700" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>

                                <div class="space-y-2 mb-8 border-t border-white/5 pt-4 text-[10px] font-black uppercase">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Points requis</span>
                                        <span class="text-white">{{ $subTable->points_min }} - {{ $subTable->points_max }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Début de série</span>
                                        <span class="text-white">{{ \Carbon\Carbon::parse($subTable->superTable->start_time)->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($myRegistrations->count() >= 2)
                                <button disabled class="w-full bg-white/5 text-gray-600 font-[1000] py-4 rounded-2xl uppercase tracking-widest text-xs cursor-not-allowed border border-white/5">
                                    Limite atteinte
                                </button>
                            @elseif($isLocked)
                                <div class="w-full bg-amber-500/10 border border-amber-500/20 text-amber-500 font-black uppercase text-[10px] tracking-widest py-4 rounded-2xl text-center">
                                    Indisponible
                                </div>
                            @elseif($isFull)
                                <div class="w-full bg-red-500/10 border border-red-500/20 text-red-500 font-black uppercase text-[10px] tracking-widest py-4 rounded-2xl text-center">
                                    Plus de place
                                </div>
                            @else
                                <form action="{{ route('player.register', $subTable->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-white text-black font-[1000] py-4 rounded-2xl uppercase tracking-widest text-xs hover:bg-indigo-600 hover:text-white transition-all transform hover:scale-[1.02]">
                                        S'inscrire
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection