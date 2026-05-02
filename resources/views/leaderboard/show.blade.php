<x-app-layout>
<div class="max-w-lg mx-auto px-4 py-8">
    <h1 class="text-2xl font-extrabold text-center mb-2">Today's Leaderboard</h1>
    <p class="text-slate-400 text-sm text-center mb-8">
        {{ now()->format('l, j F Y') }}
    </p>

    @if($topPlayers->isEmpty())
        <div class="text-center py-16 text-slate-500">
            <div class="text-5xl mb-4">🏆</div>
            <p class="text-lg font-semibold text-slate-300">No winners yet today</p>
            <p class="mt-1">Be the first to solve it!</p>
            <a href="{{ route('game') }}" class="mt-4 inline-block bg-emerald-500 hover:bg-emerald-400 text-white font-semibold px-5 py-2.5 rounded-xl transition">Play now</a>
        </div>
    @else
        <div class="space-y-2">
            @foreach($topPlayers as $index => $game)
                @php $isUser = auth()->id() === $game->user_id; @endphp
                <div class="flex items-center gap-3 bg-slate-800 rounded-xl px-4 py-3 {{ $isUser ? 'ring-2 ring-emerald-500' : '' }}">
                    <div class="w-7 text-center font-extrabold text-lg
                        {{ $index === 0 ? 'text-yellow-400' : ($index === 1 ? 'text-slate-300' : ($index === 2 ? 'text-amber-600' : 'text-slate-600')) }}">
                        @if($index === 0) 🥇
                        @elseif($index === 1) 🥈
                        @elseif($index === 2) 🥉
                        @else {{ $index + 1 }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="font-semibold text-white {{ $isUser ? 'text-emerald-400' : '' }}">
                            {{ $game->user->name }}
                            @if($isUser) (you)@endif
                        </span>
                    </div>
                    <div class="flex gap-0.5">
                        @foreach($game->guesses as $g)
                            <div class="flex gap-px">
                                @foreach($g['result'] as $state)
                                    <div class="w-2.5 h-2.5 rounded-sm
                                        {{ $state === 'correct' ? 'bg-emerald-500' : ($state === 'present' ? 'bg-amber-500' : 'bg-slate-600') }}">
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="text-emerald-400 font-bold tabular-nums">{{ $game->attempts }}/6</div>
                </div>
            @endforeach
        </div>

        @if($userGame && $userGame->status === 'playing')
            <div class="mt-6 text-center">
                <p class="text-slate-400 text-sm mb-3">You haven't finished today yet!</p>
                <a href="{{ route('game') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white font-semibold px-5 py-2.5 rounded-xl transition inline-block">Continue playing</a>
            </div>
        @elseif(! auth()->check())
            <div class="mt-6 text-center">
                <a href="{{ route('register') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white font-semibold px-5 py-2.5 rounded-xl transition inline-block">Sign up to compete</a>
            </div>
        @endif
    @endif
</div>
</x-app-layout>
