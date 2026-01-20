@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black">
    <div class="max-w-7xl mx-auto px-6">
        
        <div class="mb-10">
            <h2 class="text-3xl font-black text-white uppercase italic tracking-tighter">
                Gestion des séries : <span class="text-indigo-500">{{ $superTable->name }}</span>
            </h2>
            <div class="flex gap-4 mt-2">
                <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest italic border-r border-white/10 pr-4">
                    Horaire : {{ \Carbon\Carbon::parse($superTable->start_time)->format('H:i') }}
                </span>
                <span class="text-indigo-500 text-[10px] font-black uppercase tracking-widest italic">
                    Quota Global : {{ $superTable->max_players }} places
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-5">
                <div class="bg-[#0f0f0f] border border-white/5 p-8 rounded-[2rem] shadow-2xl sticky top-32">
                    <h3 class="text-white font-black uppercase italic text-sm mb-6 tracking-widest">Nouvelle Série</h3>
                    
                    <form action="{{ route('admin.sub_tables.store', $superTable->id) }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-[10px] text-gray-600 uppercase font-black mb-2 tracking-widest">Nom (ex: Tableau Elite)</label>
                            <input type="text" name="label" required class="w-full bg-black border-white/10 text-white rounded-xl px-4 py-4 focus:border-indigo-500 focus:ring-0 font-bold italic uppercase">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-gray-600 uppercase font-black mb-2 tracking-widest">Frais (€)</label>
                                <input type="number" name="entry_fee" step="0.5" required class="w-full bg-black border-white/10 text-white rounded-xl px-4 py-4 focus:border-indigo-500 focus:ring-0 font-black text-lg">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-600 uppercase font-black mb-2 tracking-widest">Points Max</label>
                                <input type="number" name="points_max" required class="w-full bg-black border-white/10 text-indigo-500 rounded-xl px-4 py-4 focus:border-indigo-500 focus:ring-0 font-black text-lg">
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 pt-4">
                            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase text-xs tracking-[0.3em] rounded-xl transition-all shadow-lg shadow-indigo-600/20">
                                Valider la série
                            </button>
                            <a href="{{ route('admin.super_tables.create', $superTable->tournament_id) }}" class="w-full py-4 bg-white/5 text-gray-500 hover:text-white text-center rounded-xl font-black uppercase text-[10px] tracking-widest transition-all">
                                Retour au planning
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-7">
                <h3 class="text-gray-600 font-black uppercase text-[10px] mb-6 tracking-[0.3em] flex items-center gap-3">
                    <span class="w-8 h-[1px] bg-white/10"></span>
                    Séries configurées dans ce bloc
                </h3>

                <div class="space-y-4">
                    @forelse($superTable->subTables as $subTable)
                        <div class="flex items-center justify-between bg-[#0a0a0a] border border-white/5 p-6 rounded-2xl group hover:border-indigo-500/30 transition-all shadow-xl">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 bg-indigo-600/10 rounded-xl flex items-center justify-center border border-indigo-500/20">
                                    <span class="text-indigo-500 font-black italic">{{ substr($subTable->label, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h4 class="text-white font-black uppercase italic text-lg tracking-tight">{{ $subTable->label }}</h4>
                                    <div class="flex gap-4 mt-1">
                                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                            Frais : <span class="text-gray-300">{{ number_format($subTable->entry_fee, 2) }}€</span>
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                            Limite : <span class="text-indigo-400">{{ $subTable->points_max }} pts</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.sub_tables.destroy', $subTable->id) }}" method="POST" onsubmit="return confirm('Supprimer cette série ?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-800 hover:text-red-500 transition-colors p-2 bg-black rounded-lg border border-white/5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="py-20 text-center border-2 border-dashed border-white/5 rounded-[2rem]">
                            <p class="text-gray-700 font-black uppercase text-[10px] tracking-widest italic">Aucune série pour le moment</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection