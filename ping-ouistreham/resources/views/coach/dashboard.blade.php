@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black pt-32 pb-20 px-6">
    <div class="max-w-7xl mx-auto">
        
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

        {{-- SECTION 1 : INSCRIRE L'ÉQUIPE AUX TABLEAUX --}}
        <div class="mb-20">
            <h3 class="text-2xl font-black uppercase italic mb-8 text-white flex items-center gap-3">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                Inscrire mon équipe
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($availableSubTables as $table)
                    @php
                        $max = (int) $table->superTable->max_players;
                        $current = $table->superTable->registrations->where('status', 'confirmed')->count();
                        $percentage = $max > 0 ? round(($current / $max) * 100) : 0;
                        // On définit le statut complet
                        $isFull = $current >= $max;
                    @endphp

                    <div class="bg-[#0f0f0f] border {{ $isFull ? 'border-red-500/40' : 'border-white/5' }} p-8 rounded-[2.5rem] shadow-2xl flex flex-col group hover:border-indigo-500/30 transition-all relative overflow-hidden">
                        
                        @if($isFull)
                            <div class="absolute top-4 right-[-35px] bg-red-600 text-white text-[8px] font-black py-1 px-10 transform rotate-45 uppercase tracking-tighter shadow-xl">
                                COMPLET / FULL
                            </div>
                        @endif

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-black uppercase italic text-white group-hover:text-indigo-400 transition-colors">
                                    {{ $table->label }}
                                </h3>
                                <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase italic tracking-widest">
                                    {{ \Carbon\Carbon::parse($table->superTable->start_time)->format('H:i') }} — {{ $table->entry_fee }}€
                                </p>
                            </div>
                            <span class="text-[10px] font-black px-3 py-1 bg-indigo-500/10 rounded-full text-indigo-500 border border-indigo-500/20 uppercase">
                                {{ $table->points_max }} pts
                            </span>
                        </div>

                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[9px] font-black uppercase tracking-widest {{ $isFull ? 'text-red-500' : 'text-gray-500' }}">
                                    @if($isFull) INSCRIPTIONS BLOQUÉES @else REMPLISSAGE @endif
                                </span>
                                <span class="text-[10px] font-black {{ $isFull ? 'text-red-500' : 'text-white' }}">
                                    {{ $current }} / {{ $max }} ({{ $percentage }}%)
                                </span>
                            </div>
                            <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-1000 {{ $isFull ? 'bg-red-600' : 'bg-indigo-600' }}" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        {{-- LISTE DES JOUEURS DÉJÀ INSCRITS --}}
                        <div class="flex flex-wrap gap-2 mb-8 min-h-[32px]">
                            @php
                                $teamIds = auth()->user()->students->pluck('id')->push(auth()->id());
                                $teamInscribed = $table->registrations->whereIn('user_id', $teamIds);
                            @endphp

                            @foreach($teamInscribed as $registration)
                                <button type="button" 
                                    onclick="confirmUnregister('{{ $registration->user_id }}', '{{ $table->id }}', '{{ $registration->player_firstname }} {{ $registration->player_lastname }}')"
                                    class="flex items-center gap-2 text-[9px] bg-white/5 text-gray-400 border border-white/5 px-3 py-1.5 rounded-xl font-black uppercase tracking-widest hover:bg-red-500/20 hover:text-red-500 hover:border-red-500/30 transition-all group/badge">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_5px_green] group-hover/badge:bg-red-500"></span>
                                    {{ $registration->player_firstname }} {{ substr($registration->player_lastname, 0, 1) }}.
                                    <span class="opacity-0 group-hover/badge:opacity-100 ml-1 text-xs">×</span>
                                </button>
                            @endforeach
                        </div>

                        <form action="{{ route('coach.register_player') }}" method="POST" class="mt-auto pt-6 border-t border-white/5">
                            @csrf
                            <input type="hidden" name="sub_table_id" value="{{ $table->id }}">
                            <div class="space-y-4">
                                <select name="player_id" required {{ $isFull ? 'disabled' : '' }}
                                        class="w-full bg-black border border-white/10 rounded-2xl px-5 py-4 text-white text-[10px] font-black uppercase tracking-widest focus:border-indigo-500 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="" disabled selected>Choisir un joueur...</option>
                                    @if(auth()->user()->points <= $table->points_max)
                                        <option value="{{ auth()->id() }}">Moi-même (Coach) - {{ auth()->user()->points }} pts</option>
                                    @endif
                                    @foreach(auth()->user()->students as $student)
                                        @php 
                                            $hasTwoTables = $student->registrations->where('subTable.superTable.tournament_id', $table->superTable->tournament_id)->count() >= 2;
                                            $tooManyPoints = $student->points > $table->points_max || $student->points < $table->points_min;
                                        @endphp
                                        <option value="{{ $student->id }}" {{ ($hasTwoTables || $tooManyPoints) ? 'disabled' : '' }}>
                                            {{ $student->name }} ({{ $student->points }} pts) {{ $hasTwoTables ? '[MAX 2]' : ($tooManyPoints ? '[PTS HORS LIMITES]' : '') }}
                                        </option>
                                    @endforeach
                                </select>

                                @if($isFull)
                                    <div class="w-full bg-red-500/10 border border-red-500/20 text-red-500 font-black uppercase text-[9px] tracking-widest py-5 rounded-2xl text-center">
                                        Tableau complet
                                    </div>
                                @else
                                    <button type="submit" class="w-full bg-indigo-600 shadow-indigo-500/20 hover:bg-white hover:text-black text-white font-black uppercase text-[10px] tracking-[0.3em] py-5 rounded-2xl transition-all shadow-lg">
                                        Valider l'inscription
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center border-2 border-dashed border-white/5 rounded-[3rem]">
                        <p class="text-gray-700 uppercase font-black tracking-[0.4em] text-sm italic">Aucune compétition ouverte</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- SECTION 2 : LISTE DES ÉLÈVES --}}
        <div class="mb-20">
            <h3 class="text-2xl font-black uppercase italic mb-8 text-white flex items-center gap-3">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                Mes Élèves & Accès
            </h3>
            <div class="bg-[#0f0f0f] border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-gray-500 text-[10px] uppercase font-black tracking-widest">
                            <th class="px-8 py-6">Nom de l'élève</th>
                            <th class="px-8 py-6">Identifiant Email</th>
                            <th class="px-8 py-6">Mot de passe</th>
                            <th class="px-8 py-6 text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse(auth()->user()->students as $student)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-8 py-6 text-white font-bold uppercase italic">{{ $student->name }}</td>
                                <td class="px-8 py-6 text-indigo-400 font-mono text-xs">{{ $student->email }}</td>
                                <td class="px-8 py-6">
                                    <span class="bg-white/5 px-3 py-2 rounded-lg text-gray-400 font-mono text-xs">
                                        {{ $student->password_plain ?? '********' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right text-white font-black tracking-tighter">
                                    {{ $student->points }} <span class="text-gray-600 text-[10px]">PTS</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-10 text-center text-gray-600 uppercase text-xs font-bold tracking-widest italic">Aucun élève enregistré</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SECTION 3 : FORMULAIRE D'AJOUT --}}
        <div id="add-student" class="max-w-3xl mx-auto">
            <div class="bg-[#1a1a1a] border border-white/10 p-10 rounded-[3rem] shadow-3xl">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-black uppercase italic text-white tracking-tighter">
                        NOUVEL <span class="text-indigo-500">ÉLÈVE</span>
                    </h2>
                    <p class="text-gray-600 text-[10px] uppercase font-bold tracking-widest mt-1 italic">// EMAIL ET PASSWORD GÉNÉRÉS AUTOMATIQUEMENT</p>
                </div>

                <form action="{{ route('coach.add_student') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">Nom complet</label>
                            <input type="text" name="name" required placeholder="EX: JEAN DUPONT" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">N° Licence</label>
                            <input type="text" name="license_number" required placeholder="EX: 1422334" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">Points FFTT</label>
                            <input type="number" name="points" required placeholder="500" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] uppercase font-black text-gray-400 tracking-widest ml-1 mb-2 block">Club</label>
                            <input type="text" name="club" placeholder="EX: TT OUISTREHAM" class="w-full bg-black border border-white/10 rounded-2xl px-6 py-4 text-white text-sm focus:border-indigo-500 transition-all outline-none">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-white text-black font-black uppercase text-xs tracking-[0.3em] py-6 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all duration-500 transform hover:-translate-y-1 shadow-xl">
                        Enregistrer et générer les accès
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- FORMULAIRE CACHÉ POUR LA DÉSINSCRIPTION --}}
<form id="unregister-form" action="{{ route('coach.unregister_player') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="player_id" id="unreg-player-id">
    <input type="hidden" name="sub_table_id" id="unreg-table-id">
</form>

<script>
function confirmUnregister(playerId, tableId, playerName) {
    if (confirm("Désinscrire " + playerName.toUpperCase() + " de ce tableau ?")) {
        document.getElementById('unreg-player-id').value = playerId;
        document.getElementById('unreg-table-id').value = tableId;
        document.getElementById('unregister-form').submit();
    }
}
</script>
@endsection