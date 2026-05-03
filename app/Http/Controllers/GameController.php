<?php

namespace App\Http\Controllers;

use App\Events\GameActivityEvent;
use App\Models\DailyWord;
use App\Models\Game;
use App\Services\WordleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class GameController extends Controller
{
    public function __construct(private WordleService $wordle) {}

    public function show(Request $request): View
    {
        $dailyWord = DailyWord::today();

        if (! $dailyWord) {
            return view('game.unavailable');
        }

        $game = null;
        if ($request->user()) {
            $game = Game::firstOrCreate(
                ['user_id' => $request->user()->id, 'daily_word_id' => $dailyWord->id],
                ['guesses' => [], 'status' => 'playing']
            );
        }

        $solverCount = Game::where('daily_word_id', $dailyWord->id)
            ->where('status', 'won')
            ->count();

        return view('game.show', compact('dailyWord', 'game', 'solverCount'));
    }

    public function guess(Request $request): JsonResponse
    {
        $request->validate(['guess' => ['required', 'string', 'size:5', 'alpha']]);

        $dailyWord = DailyWord::today();

        if (! $dailyWord) {
            return response()->json(['error' => 'No word available today.'], 422);
        }

        $user = $request->user();
        $game = Game::firstOrCreate(
            ['user_id' => $user->id, 'daily_word_id' => $dailyWord->id],
            ['guesses' => [], 'status' => 'playing']
        );

        if ($game->isFinished()) {
            return response()->json(['error' => 'Game already finished.'], 422);
        }

        $guess  = strtolower($request->guess);
        $result = $this->wordle->evaluate($guess, $dailyWord->word);

        $guesses   = $game->guesses;
        $guesses[] = ['word' => $guess, 'result' => $result];

        $won     = ! in_array('absent', $result) && ! in_array('present', $result);
        $lost    = ! $won && count($guesses) >= 6;
        $status  = $won ? 'won' : ($lost ? 'lost' : 'playing');
        $finished = $won || $lost;

        $game->update([
            'guesses'      => $guesses,
            'attempts'     => count($guesses),
            'status'       => $status,
            'completed_at' => $finished ? now() : null,
        ]);

        if ($finished) {
            $this->updateUserStats($user, $game, $won, count($guesses), $dailyWord->date);
        }

        $this->broadcastActivity($user->name, $result, count($guesses), $status);

        return response()->json([
            'result'    => $result,
            'status'    => $status,
            'attempts'  => count($guesses),
            'shareText' => $finished ? $game->shareText() : null,
            'word'      => $status === 'lost' ? $dailyWord->word : null,
        ]);
    }

    private function broadcastActivity(string $name, array $result, int $attempt, string $status): void
    {
        $firstName = explode(' ', $name)[0];

        if ($status === 'won') {
            GameActivityEvent::dispatch(
                "{$firstName} cracked it in {$attempt}/6!",
                '🎉',
                'won'
            );
            return;
        }

        if ($status === 'lost') {
            GameActivityEvent::dispatch(
                "{$firstName} didn't get it today",
                '😔',
                'lost'
            );
            return;
        }

        if ($attempt === 1) {
            GameActivityEvent::dispatch(
                "{$firstName} just started playing",
                '🟩',
                'started'
            );
            return;
        }

        $correct = count(array_filter($result, fn($r) => $r === 'correct'));
        $present = count(array_filter($result, fn($r) => $r === 'present'));

        if ($correct >= 3) {
            GameActivityEvent::dispatch(
                "{$firstName} has {$correct}/5 letters in the right place!",
                '🔥',
                'progress'
            );
        } elseif ($correct + $present >= 3) {
            GameActivityEvent::dispatch(
                "{$firstName} has found " . ($correct + $present) . " of the letters",
                '💡',
                'progress'
            );
        }
    }

    private function updateUserStats($user, Game $game, bool $won, int $attempts, $date): void
    {
        $dist = $user->guess_distribution ?? ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];

        $yesterday  = Carbon::parse($date)->subDay()->toDateString();
        $lastPlayed = $user->last_played_date?->toDateString();
        $streak     = $user->current_streak;

        if ($won) {
            $dist[(string) $attempts] = ($dist[(string) $attempts] ?? 0) + 1;
            $streak = ($lastPlayed === $yesterday) ? $streak + 1 : 1;
        } else {
            $streak = 0;
        }

        $user->forceFill([
            'games_played'       => $user->games_played + 1,
            'games_won'          => $user->games_won + ($won ? 1 : 0),
            'current_streak'     => $streak,
            'max_streak'         => max($user->max_streak, $streak),
            'guess_distribution' => $dist,
            'last_played_date'   => $date,
        ])->save();
    }
}
