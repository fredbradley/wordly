<?php

namespace App\Console\Commands;

use App\Models\Word;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('wordly:word {action : add or remove} {word : The 5-letter word}')]
#[Description('Manage the Wordly word bank. Actions: add, remove.')]
class WordCommand extends Command
{
    public function handle(): int
    {
        $action = strtolower($this->argument('action'));
        $word   = strtolower(trim($this->argument('word')));

        if (! in_array($action, ['add', 'remove'])) {
            $this->error("Unknown action '{$action}'. Use 'add' or 'remove'.");
            return self::FAILURE;
        }

        if (strlen($word) !== 5 || ! ctype_alpha($word)) {
            $this->error("'{$word}' is not a valid 5-letter word.");
            return self::FAILURE;
        }

        return $action === 'add' ? $this->addWord($word) : $this->removeWord($word);
    }

    private function addWord(string $word): int
    {
        $existing = Word::withTrashed()->where('word', $word)->first();

        if ($existing && ! $existing->trashed()) {
            $this->warn("'{$word}' is already in the word bank.");
            return self::SUCCESS;
        }

        if ($existing && $existing->trashed()) {
            $existing->restore();
            $this->info("'{$word}' restored to the word bank.");
            return self::SUCCESS;
        }

        Word::create(['word' => $word]);
        $this->info("'{$word}' added to the word bank.");
        return self::SUCCESS;
    }

    private function removeWord(string $word): int
    {
        $existing = Word::where('word', $word)->first();

        if (! $existing) {
            $this->warn("'{$word}' not found in the active word bank.");
            return self::FAILURE;
        }

        $existing->delete();
        $this->info("'{$word}' soft-deleted from the word bank (historical game data preserved).");
        return self::SUCCESS;
    }
}
