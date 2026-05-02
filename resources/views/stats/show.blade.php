<x-app-layout>
<div class="max-w-lg mx-auto px-4 py-8">
    <h1 class="text-2xl font-extrabold text-center mb-8">My Statistics</h1>

    <div class="grid grid-cols-4 gap-4 mb-8 text-center">
        <div class="bg-slate-800 rounded-xl p-4">
            <div class="text-3xl font-extrabold text-white">{{ $user->games_played }}</div>
            <div class="text-xs text-slate-400 mt-1">Played</div>
        </div>
        <div class="bg-slate-800 rounded-xl p-4">
            <div class="text-3xl font-extrabold text-emerald-400">{{ $user->winRate() }}</div>
            <div class="text-xs text-slate-400 mt-1">Win %</div>
        </div>
        <div class="bg-slate-800 rounded-xl p-4">
            <div class="text-3xl font-extrabold text-amber-400">{{ $user->current_streak }}</div>
            <div class="text-xs text-slate-400 mt-1">Streak</div>
        </div>
        <div class="bg-slate-800 rounded-xl p-4">
            <div class="text-3xl font-extrabold text-amber-400">{{ $user->max_streak }}</div>
            <div class="text-xs text-slate-400 mt-1">Best</div>
        </div>
    </div>

    <div class="bg-slate-800 rounded-xl p-5 mb-8">
        <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">Guess Distribution</h2>
        @php $dist = $user->guess_distribution ?? []; $max = max(array_values($dist) ?: [1]); @endphp
        @foreach(range(1, 6) as $n)
            @php $count = $dist[(string)$n] ?? 0; $pct = $max > 0 ? max(6, ($count/$max)*100) : 6; @endphp
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-slate-400 text-sm w-3 text-center">{{ $n }}</span>
                <div class="flex-1 bg-slate-700 rounded overflow-hidden h-7">
                    <div class="h-full bg-emerald-600 flex items-center justify-end pr-2 text-white text-sm font-bold transition-all"
                         style="width: {{ $pct }}%">
                        {{ $count }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center">
        <a href="{{ route('game') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white font-semibold px-6 py-3 rounded-xl transition inline-block">
            Play Today's Wordly
        </a>
    </div>
</div>
</x-app-layout>
