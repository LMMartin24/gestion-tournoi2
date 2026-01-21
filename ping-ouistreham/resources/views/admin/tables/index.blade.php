@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black pt-24 pb-12 px-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-black uppercase italic text-white mb-12 flex items-center gap-4">
            <span class="w-3 h-10 bg-indigo-600 rounded-full"></span>
            Génération des Tableaux : {{ $tournament->name }}
        </h1>

        <div class="space-y-12">
            @foreach($superTables as $superTable)
                <div class="bg-[#0f0f0f] border border-white/5 rounded-[2.5rem] p-8 shadow-2xl">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                        <div>
                            <h2 class="text-2xl font-black uppercase italic text-white">{{ $superTable->label }}</h2>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">
                                {{ \Carbon\Carbon::parse($superTable->start_time)->format('H:i') }} — Max {{ $superTable->max_players }} joueurs
                            </p>
                        </div>
                        
                        <form action="{{ route('admin.tables.generate', $superTable->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 hover:bg-white hover:text-black text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all shadow-lg shadow-indigo-600/20">
                                Générer ce créneau
                            </button>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($superTable->subTables as $subTable)
                            <div class="bg-black border border-white/5 rounded-2xl p-6">
                                <div class="flex justify-between items-center mb-4 border-b border-white/5 pb-3">
                                    <h3 class="text-indigo-400 font-black uppercase text-sm italic">{{ $subTable->label }}</h3>
                                    <span class="text-[10px] text-gray-500 font-bold">{{ $subTable->users->count() }} inscrits</span>
                                </div>
                                
                                <ul class="space-y-2">
                                    @forelse($subTable->users->sortByDesc('points') as $player)
                                        <li class="flex justify-between items-center text-[11px] text-gray-300 group">
                                            <span class="font-bold uppercase">{{ $player->name }}</span>
                                            <span class="text-gray-600 font-mono">{{ $player->points }} pts</span>
                                        </li>
                                    @empty
                                        <li class="text-gray-700 text-[10px] uppercase font-bold text-center py-2 italic">Aucun inscrit</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection