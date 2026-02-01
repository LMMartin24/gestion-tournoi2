@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-black pt-32 pb-20 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-[#1a1a1a] rounded-[3rem] p-12 border border-white/5 relative overflow-hidden mb-12 shadow-2xl">
            <div class="relative z-10">
                <h1 class="text-6xl font-black text-white uppercase italic tracking-tighter">{{ $tournament->name }}</h1>
                <div class="flex flex-wrap gap-6 mt-6 text-gray-400 font-bold uppercase text-[10px] tracking-widest">
                    <span>📍 {{ $tournament->location }}</span>
                    <span>📅 {{ \Carbon\Carbon::parse($tournament->date)->format('d/m/Y') }}</span>
                    <span class="text-red-500">⏳ Clôture le {{ \Carbon\Carbon::parse($tournament->registration_deadline)->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="absolute -right-10 -bottom-10 text-white/5 text-9xl font-black italic">OPEN</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($tournament->superTables as $slot)
                @foreach($slot->subTables as $subTable)
                <div class="bg-[#111] border border-white/10 p-8 rounded-[2.5rem] flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-2xl font-black text-white italic uppercase">Série {{ $subTable->label }}</h3>
                            <span class="text-indigo-500 font-black">{{ $subTable->entry_fee }}€</span>
                        </div>
                        <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-6">
                            {{ $subTable->points_min }} - {{ $subTable->points_max }} pts
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                            @php 
                                $count = $subTable->registrations->count();
                                $max = $slot->max_players;
                                $percent = ($count / $max) * 100;
                            @endphp
                            <div class="bg-indigo-600 h-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="flex justify-between text-[9px] font-black uppercase tracking-tighter">
                            <span class="text-gray-500">{{ $count }} inscrits</span>
                            <span class="text-white">{{ $max - $count }} places libres</span>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
@endsection