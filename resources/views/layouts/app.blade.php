<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Wordly') }} — Daily Word Puzzle</title>
        <meta name="description" content="Guess the hidden 5-letter word in 6 tries. A new word every day!">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full bg-slate-950 text-white">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-slate-800">
                <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between">
                    <a href="{{ route('game') }}" class="text-2xl font-extrabold tracking-tight text-emerald-400">Wordly</a>
                    <nav class="flex items-center gap-4 text-sm font-medium">
                        <a href="{{ route('leaderboard') }}" class="text-slate-400 hover:text-white transition">Board</a>
                        @auth
                            <a href="{{ route('stats') }}" class="text-slate-400 hover:text-white transition">Stats</a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button class="text-slate-400 hover:text-white transition">Log out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition">Log in</a>
                            <a href="{{ route('register') }}" class="bg-emerald-500 hover:bg-emerald-400 text-white px-3 py-1.5 rounded-lg transition">Sign up</a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-800 py-4 text-center text-xs text-slate-600">
                &copy; {{ date('Y') }} Wordly. A new word every day.
            </footer>
        </div>
    </body>
</html>
