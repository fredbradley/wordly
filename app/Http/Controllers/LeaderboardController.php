<?php

namespace App\Http\Controllers;

use App\Models\DailyWord;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function show(Request $request): View
    {
        $dailyWord = DailyWord::today();

        $topPlayers = collect();
        if ($dailyWord) {
            $topPlayers = Game::with('user')
                ->where('daily_word_id', $dailyWord->id)
                ->where('status', 'won')
                ->orderBy('attempts')
                ->orderBy('completed_at')
                ->take(20)
                ->get();
        }

        $userGame = null;
        if ($request->user() && $dailyWord) {
            $userGame = Game::where('user_id', $request->user()->id)
                ->where('daily_word_id', $dailyWord->id)
                ->first();
        }

        return view('leaderboard.show', compact('topPlayers', 'dailyWord', 'userGame'));
    }
}
