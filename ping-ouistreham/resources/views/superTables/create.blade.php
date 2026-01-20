@extends('layouts.app')

@section('content')
<div class="min-h-screen pt-32 pb-20 bg-black relative">
    <div class="max-w-5xl mx-auto px-6">
        
        <div class="mb-12">
            <h2 class="text-4xl font-black text-white uppercase italic tracking-tighter">
                Configuration des <span class="text-indigo-500">Tableaux</span>
            </h2>
            <p class="text-gray-500 text-sm mt-2 uppercase tracking-widest font-bold">
                Tournoi : {{ $tournament->name }} // {{ \Carbon\Carbon::parse($tournament->date)->format('d.m.Y') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-[#1a1a1a] border border-white/10 p-6 rounded-3xl sticky top-32">
                    <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6">Ajouter une série</h3>
                    
                    <form action="{{ route('admin.categories.store', $tournament->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Nom du tableau</label>
                            <input type="text" name="name" placeholder="Ex: Série masculine -1200 pts" 
                                class="w-full bg-black border-white/5 text-white rounded-xl px-4 py-3 focus:ring-indigo-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Tarif (€)</label>
                                <input type="number" name="entry_fee" step="0.5" placeholder="8.00" 
                                    class="w-full bg-black border-white/5 text-white rounded-xl px-4 py-3">
                            </div>
                            <div>
                                <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Max Joueurs</label>
                                <input type="number" name="max_players" placeholder="32" 
                                    class="w-full bg-black border-white/5 text-white rounded-xl px-4 py-3">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] text-gray-500 uppercase font-black mb-1 block">Heure de début</label>
                            <input type="time" name="start_time" 
                                class="w-full bg-black border-white/5 text-white rounded-xl px-4 py-3">
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white font-black uppercase text-xs py-4 rounded-xl hover:bg-indigo-500 transition">
                            Ajouter au tournoi
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6">Tableaux enregistrés ({{ $tournament->categories->count() }})</h3>
                
                @forelse($tournament->categories as $category)
                    <div class="bg-white/5 border border-white/5 p-5 rounded-2xl flex justify-between items-center group hover:border-indigo-500/50 transition">
                        <div>
                            <p class="text-white font-bold text-lg italic uppercase">{{ $category->name }}</p>
                            <div class="flex gap-4 mt-1">
                                <span class="text-[10px] text-gray-500 uppercase">💰 {{ $category->entry_fee }}€</span>
                                <span class="text-[10px] text-gray-500 uppercase">👥 {{ $category->max_players }} places</span>
                                <span class="text-[10px] text-gray-500 uppercase">🕒 {{ \Carbon\Carbon::parse($category->start_time)->format('H:i') }}</span>
                            </div>
                        </div>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="opacity-0 group-hover:opacity-100 p-2 text-red-500 hover:bg-red-500/10 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="border-2 border-dashed border-white/5 rounded-3xl p-12 text-center">
                        <p class="text-gray-600 uppercase text-xs font-bold tracking-widest">Aucun tableau pour le moment</p>
                    </div>
                @endforelse

                @if($tournament->categories->count() > 0)
                    <div class="pt-6">
                        <a href="{{ route('admin.tournaments.show', $tournament->id) }}" class="block w-full text-center bg-white text-black font-black uppercase text-xs py-5 rounded-full hover:scale-[1.02] transition">
                            Finaliser la configuration
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection