@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black">
    <div class="max-w-6xl mx-auto px-6">
        
        <div class="mb-12 flex justify-between items-end">
            <div>
                <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                    Création <span class="text-indigo-500">Super Tableaux</span>
                </h2>
                <p class="text-gray-500 text-sm mt-2 uppercase tracking-widest font-bold italic">
                    Tournoi : {{ $tournament->name }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-5">
                <div class="bg-[#0f0f0f] border border-white/5 p-8 rounded-[2.5rem] shadow-2xl">
                    <form action="{{ route('admin.tables.store', $tournament->id) }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-[10px] text-gray-600 uppercase font-black mb-2 tracking-widest uppercase italic">Nom du bloc</label>
                            <input type="text" name="name" placeholder="ex: Session A" required 
                                class="w-full bg-black border-white/10 text-white rounded-xl px-4 py-4 focus:border-indigo-500 focus:ring-0 font-bold">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] text-gray-600 uppercase font-black mb-2 tracking-widest italic uppercase">Heure</label>
                                <input type="time" name="start_time" required 
                                    class="w-full bg-black border-white/10 text-white rounded-xl px-4 py-4 focus:border-indigo-500 focus:ring-0 text-xl font-black">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-600 uppercase font-black mb-2 tracking-widest italic uppercase">Places Max</label>
                                <input type="number" name="max_players" placeholder="72" required 
                                    class="w-full bg-black border-white/10 text-white rounded-xl px-4 py-4 focus:border-indigo-500 focus:ring-0 text-xl font-black text-indigo-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-white hover:text-black text-white font-black uppercase text-xs tracking-[0.3em] rounded-xl transition-all duration-500 shadow-lg shadow-indigo-600/20">
                            Enregistrer le Super Tableau
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-4">
                <h3 class="text-[10px] text-gray-500 uppercase font-black tracking-[.3em] mb-6">// PLANNING DES BLOCS</h3>
                
                @forelse($superTables as $superTable)
                <div class="flex items-center justify-between bg-[#0a0a0a] border border-white/5 p-6 rounded-2xl group hover:border-red-500/30 transition-all shadow-xl">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 bg-indigo-600/10 rounded-xl flex flex-col items-center justify-center border border-indigo-500/20">
                            <span class="text-indigo-500 font-black italic text-lg">{{ $subTable->points_max }}</span>
                            <span class="text-[8px] text-indigo-300 uppercase font-bold tracking-tighter">pts</span>
                        </div>
                        
                        <div>
                            <h4 class="text-white font-black uppercase italic text-lg tracking-tight group-hover:text-indigo-400 transition-colors">
                                {{ $subTable->label }}
                            </h4>
                            <p class="text-[10px] text-green-500 font-bold uppercase tracking-widest mt-1">
                                Frais : {{ number_format($subTable->entry_fee, 2) }}€
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('admin.sub_tables.destroy', $subTable->id) }}" method="POST" 
                        onsubmit="return confirm('Es-tu sûr de vouloir supprimer cette série ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-3 bg-black hover:bg-red-600/20 text-gray-700 hover:text-red-500 border border-white/5 hover:border-red-500/50 rounded-xl transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
                @empty
                    <div class="py-20 text-center border-2 border-dashed border-white/5 rounded-[2.5rem]">
                        <p class="text-gray-700 font-bold uppercase text-[10px] tracking-[0.3em] italic">Aucun bloc programmé</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection