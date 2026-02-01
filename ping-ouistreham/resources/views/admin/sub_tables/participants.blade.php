@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black text-white">
    <div class="max-w-6xl mx-auto px-6">
        
        {{-- RETOUR --}}
        <div class="mb-8">
            <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-white transition-colors flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
                Retour à la config
            </a>
        </div>

        {{-- HEADER --}}
        <div class="mb-12 flex justify-between items-end">
            <div>
                <p class="text-indigo-500 text-[10px] font-black uppercase tracking-[0.3em] mb-2">// LISTE DES ENGAGÉS</p>
                <h1 class="text-5xl font-black uppercase italic tracking-tighter">
                    {{ $subTable->label }} <span class="text-gray-700">/</span> <span class="text-white/50 text-3xl">{{ $subTable->superTable->name }}</span>
                </h1>
            </div>
            <div class="text-right">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Total</p>
                <p class="text-4xl font-black italic text-white">{{ $subTable->registrations->count() }}</p>
            </div>
        </div>

        {{-- TABLEAU DES INSCRITS --}}
        <div class="bg-[#111] border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5">
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-500">Joueur</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-500">Contact</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-500 text-center">Points</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-500 text-center">Statut</th>
                        <th class="px-8 py-6 text-[10px] font-black uppercase tracking-widest text-gray-500 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($subTable->registrations->sortBy('created_at') as $reg)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-6">
                                <p class="text-lg font-black uppercase italic leading-none">{{ $reg->player_firstname }} {{ $reg->player_lastname }}</p>
                                <p class="text-[10px] font-bold text-gray-600 uppercase mt-1">Licence : {{ $reg->player_license }}</p>
                            </td>
                            <td class="px-8 py-6 italic">
                                <p class="text-xs font-bold text-gray-300">{{ $reg->user->email }}</p>
                                <p class="text-[10px] font-black text-indigo-500/50 uppercase">{{ $reg->user->phone ?? 'Pas de tel' }}</p>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="font-mono font-black text-white bg-white/5 px-3 py-1 rounded-lg">{{ $reg->player_points }}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($reg->status === 'confirmed')
                                    <span class="text-[9px] font-black uppercase px-3 py-1 bg-green-500/10 text-green-500 border border-green-500/20 rounded-full italic">Confirmé</span>
                                @else
                                    <span class="text-[9px] font-black uppercase px-3 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-full italic tracking-tighter">Liste d'attente</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <form action="{{ route('admin.registrations.cancel', $reg->id) }}" method="POST" onsubmit="return confirm('Désinscrire ce joueur ? (Le suivant en liste d\'attente sera repêché)')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500/30 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center text-gray-700 font-black uppercase italic tracking-[0.2em]">Aucun inscrit dans ce tableau.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection