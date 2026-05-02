@php
$gameConfig = [
    'guesses'     => $game?->guesses ?? [],
    'status'      => $game?->status ?? 'playing',
    'wordLength'  => 5,
    'maxAttempts' => 6,
    'isAuthed'    => auth()->check(),
    'guessUrl'    => route('game.guess'),
    'csrfToken'   => csrf_token(),
];
@endphp
<x-app-layout>
<div class="max-w-lg mx-auto px-4 py-6 flex flex-col items-center gap-4"
     x-data="wordlyGame(@js($gameConfig))"
     x-init="init()">

    {{-- Date + solver count --}}
    <div class="text-center text-slate-400 text-sm">
        {{ now()->format('l, j F Y') }}
        @if($solverCount > 0)
            &middot; <span class="text-emerald-400 font-semibold">{{ $solverCount }}</span> solved today
        @endif
    </div>

    {{-- Notification banner --}}
    <div x-show="notification" x-transition
         class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-white text-slate-900 font-semibold px-5 py-2 rounded-full shadow-lg text-sm"
         x-text="notification"></div>

    {{-- Grid --}}
    <div class="grid grid-rows-6 gap-1.5 w-full max-w-xs">
        <template x-for="row in 6" :key="row">
            <div class="grid grid-cols-5 gap-1.5">
                <template x-for="col in 5" :key="col">
                    <div class="aspect-square flex items-center justify-center border-2 text-2xl font-extrabold uppercase rounded transition-all duration-300"
                         :class="getTileClass(row - 1, col - 1)"
                         x-text="getTileLetter(row - 1, col - 1)">
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Keyboard --}}
    <div class="w-full max-w-xs flex flex-col gap-1.5 mt-2">
        <template x-for="(keyRow, ri) in keyboard" :key="ri">
            <div class="flex justify-center gap-1">
                <template x-for="key in keyRow" :key="key">
                    <button
                        @click="handleKey(key)"
                        :disabled="status !== 'playing' || loading"
                        class="rounded font-bold uppercase text-sm transition-all active:scale-95 select-none"
                        :class="getKeyClass(key)"
                        x-text="key">
                    </button>
                </template>
            </div>
        </template>
    </div>

    {{-- Auth nudge for guests --}}
    @guest
    <div class="mt-4 text-center bg-slate-800 rounded-xl p-4 text-sm max-w-xs w-full">
        <p class="text-slate-300 mb-3">
            <span class="font-semibold text-white">Sign up free</span> to save your streak, appear on the leaderboard, and share your stats.
        </p>
        <div class="flex gap-2 justify-center">
            <a href="{{ route('register') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white px-4 py-2 rounded-lg font-semibold transition text-sm">Sign up</a>
            <a href="{{ route('login') }}" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg font-semibold transition text-sm">Log in</a>
        </div>
    </div>
    @endguest

    {{-- Finished modal --}}
    <div x-show="showModal" x-transition.opacity
         class="fixed inset-0 bg-black/70 flex items-center justify-center z-40 p-4"
         @click.self="showModal = false">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-sm w-full shadow-2xl text-center">
            <div class="text-5xl mb-3" x-text="status === 'won' ? '🎉' : '😔'"></div>
            <h2 class="text-2xl font-extrabold mb-1" x-text="status === 'won' ? 'Brilliant!' : 'Better luck tomorrow'"></h2>
            <p class="text-slate-400 text-sm mb-4" x-text="status === 'won' ? 'You solved today\'s Wordly in ' + attempts + ' guess' + (attempts === 1 ? '' : 'es') + '!' : 'The word was ' + todayWord.toUpperCase() + '.'"></p>

            <div class="bg-slate-800 rounded-xl p-3 mb-4 text-left">
                <pre class="text-sm whitespace-pre-wrap font-mono text-slate-200" x-text="shareText"></pre>
            </div>

            <div class="flex gap-2 justify-center">
                <button @click="copyShare()"
                        class="flex-1 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <span x-text="copied ? 'Copied!' : 'Copy Result'"></span>
                </button>
                <a href="{{ route('leaderboard') }}"
                   class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-semibold py-2.5 rounded-xl transition flex items-center justify-center">
                    Leaderboard
                </a>
            </div>

            @auth
            <a href="{{ route('stats') }}" class="block mt-2 text-sm text-slate-400 hover:text-white transition">View my stats →</a>
            @endauth
        </div>
    </div>

</div>

<script>
function wordlyGame(config) {
    return {
        guesses:      config.guesses,
        status:       config.status,
        currentInput: '',
        loading:      false,
        notification: '',
        showModal:    false,
        copied:       false,
        attempts:     config.guesses.length,
        shareText:    '',
        todayWord:    '',
        keyboard: [
            ['q','w','e','r','t','y','u','i','o','p'],
            ['a','s','d','f','g','h','j','k','l'],
            ['⌫','z','x','c','v','b','n','m','↵'],
        ],

        init() {
            if (this.status !== 'playing') {
                this.attempts = this.guesses.length;
                setTimeout(() => { this.showModal = true; }, 400);
            }
            window.addEventListener('keydown', (e) => this.onKeydown(e));
        },

        onKeydown(e) {
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            if (e.key === 'Enter')     this.handleKey('↵');
            else if (e.key === 'Backspace') this.handleKey('⌫');
            else if (/^[a-zA-Z]$/.test(e.key)) this.handleKey(e.key.toLowerCase());
        },

        handleKey(key) {
            if (this.status !== 'playing' || this.loading) return;
            if (key === '⌫') {
                this.currentInput = this.currentInput.slice(0, -1);
            } else if (key === '↵') {
                this.submitGuess();
            } else if (this.currentInput.length < 5) {
                this.currentInput += key.toLowerCase();
            }
        },

        async submitGuess() {
            if (this.currentInput.length !== 5) {
                this.showNotification('Not enough letters');
                return;
            }

            if (!config.isAuthed) {
                this.showNotification('Sign up to save your progress!');
                window.location.href = '/register';
                return;
            }

            this.loading = true;
            try {
                const res = await fetch(config.guessUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                    },
                    body: JSON.stringify({ guess: this.currentInput }),
                });

                const data = await res.json();

                if (!res.ok) {
                    this.showNotification(data.error || 'Something went wrong');
                    this.currentInput = '';
                    return;
                }

                this.guesses = [...this.guesses, { word: this.currentInput, result: data.result }];
                this.attempts = this.guesses.length;
                this.status   = data.status;
                this.currentInput = '';

                if (data.status !== 'playing') {
                    this.shareText = data.shareText || '';
                    if (data.status === 'lost') {
                        const match = this.shareText.match(/The word was (\w+)/i);
                        this.todayWord = match ? match[1] : '';
                    }
                    setTimeout(() => { this.showModal = true; }, 600);
                }
            } catch {
                this.showNotification('Connection error — try again');
            } finally {
                this.loading = false;
            }
        },

        getTileLetter(row, col) {
            if (row < this.guesses.length) return this.guesses[row].word[col];
            if (row === this.guesses.length) return this.currentInput[col] || '';
            return '';
        },

        getTileClass(row, col) {
            const base = 'transition-colors duration-300 ';
            if (row < this.guesses.length) {
                const state = this.guesses[row].result[col];
                if (state === 'correct') return base + 'bg-emerald-600 border-emerald-600 text-white';
                if (state === 'present') return base + 'bg-amber-500 border-amber-500 text-white';
                return base + 'bg-slate-600 border-slate-600 text-white';
            }
            if (row === this.guesses.length) {
                const letter = this.currentInput[col];
                return letter
                    ? base + 'border-slate-400 text-white bg-slate-800'
                    : base + 'border-slate-700 text-transparent bg-transparent';
            }
            return base + 'border-slate-800 text-transparent bg-transparent';
        },

        getKeyClass(key) {
            const used = {};
            for (const g of this.guesses) {
                g.word.split('').forEach((l, i) => {
                    const state = g.result[i];
                    if (state === 'correct') used[l] = 'correct';
                    else if (state === 'present' && used[l] !== 'correct') used[l] = 'present';
                    else if (!used[l]) used[l] = 'absent';
                });
            }

            const wide  = key.length > 1 ? 'px-3 py-4 min-w-[2.5rem]' : 'px-2 py-4 min-w-[2rem]';
            const state = used[key];
            if (state === 'correct') return `${wide} bg-emerald-600 text-white`;
            if (state === 'present') return `${wide} bg-amber-500 text-white`;
            if (state === 'absent')  return `${wide} bg-slate-700 text-slate-400`;
            return `${wide} bg-slate-600 hover:bg-slate-500 text-white`;
        },

        showNotification(msg) {
            this.notification = msg;
            setTimeout(() => { this.notification = ''; }, 2000);
        },

        async copyShare() {
            try {
                await navigator.clipboard.writeText(this.shareText);
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            } catch {
                this.showNotification('Copy failed — please copy manually');
            }
        },
    };
}
</script>
</x-app-layout>
