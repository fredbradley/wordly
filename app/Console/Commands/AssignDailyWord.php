<?php

namespace App\Console\Commands;

use App\Models\DailyWord;
use App\Models\Word;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('wordly:assign-daily-word {--days=1 : How many upcoming days to fill}')]
#[Description('Assign random words from the word bank to upcoming daily slots.')]
class AssignDailyWord extends Command
{
    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));

        // Words already scheduled (to avoid repeats)
        $usedWords = DailyWord::pluck('word')->map(fn($w) => strtolower($w))->all();

        // Pool: active words not yet scheduled
        $pool = Word::whereNotIn('word', $usedWords)->inRandomOrder()->take($days)->pluck('word');

        if ($pool->isEmpty()) {
            $this->error('No available words in the bank. Add more with: php artisan wordly:word add {word}');
            return self::FAILURE;
        }

        // Find the next unassigned date (day after the latest scheduled date, or today)
        $latest  = DailyWord::max('date');
        $nextDate = $latest
            ? Carbon::parse($latest)->addDay()
            : Carbon::today();

        $assigned = 0;
        foreach ($pool as $word) {
            DailyWord::create(['word' => $word, 'date' => $nextDate->toDateString()]);
            $this->line("  <info>✓</info> {$nextDate->toDateString()} → <comment>{$word}</comment>");
            $nextDate->addDay();
            $assigned++;
        }

        $this->info("{$assigned} word(s) assigned.");
        return self::SUCCESS;
    }
}
