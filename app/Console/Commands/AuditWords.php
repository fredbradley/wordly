<?php

namespace App\Console\Commands;

use App\Models\Word;
use App\Services\DictionaryService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('wordly:audit-words {--dry-run : Report invalid words without deleting them}')]
#[Description('Check every word in the bank against the dictionary API and soft-delete any that are not real words.')]
class AuditWords extends Command
{
    public function handle(DictionaryService $dictionary): int
    {
        $dryRun = $this->option('dry-run');

        $words = Word::all();

        if ($words->isEmpty()) {
            $this->warn('The word bank is empty.');
            return self::SUCCESS;
        }

        $this->line('');
        $this->info(sprintf(
            'Auditing %d word%s against the dictionary API%s...',
            $words->count(),
            $words->count() === 1 ? '' : 's',
            $dryRun ? ' <comment>(dry run — nothing will be deleted)</comment>' : ''
        ));
        $this->line('');

        $invalid = [];
        $errors  = [];
        $bar     = $this->output->createProgressBar($words->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        foreach ($words as $word) {
            $bar->setMessage($word->word);

            try {
                if (! $dictionary->isRealWord($word->word)) {
                    $invalid[] = $word;
                }
            } catch (\Throwable $e) {
                $errors[] = $word->word;
            }

            // Polite pause — free API, no auth
            usleep(250_000); // 250ms between requests
            $bar->advance();
        }

        $bar->setMessage('done');
        $bar->finish();
        $this->line('');
        $this->line('');

        if (! empty($errors)) {
            $this->warn('The following words could not be checked (API error) and were skipped:');
            foreach ($errors as $w) {
                $this->line("  <comment>?</comment> {$w}");
            }
            $this->line('');
        }

        if (empty($invalid)) {
            $this->info('All words passed — the word bank is clean.');
            return self::SUCCESS;
        }

        $this->line(sprintf(
            '<error> %d invalid word%s found: </error>',
            count($invalid),
            count($invalid) === 1 ? '' : 's'
        ));

        foreach ($invalid as $word) {
            $this->line("  <fg=red>✗</> {$word->word}");
        }
        $this->line('');

        if ($dryRun) {
            $this->warn('Dry run — no words were deleted. Run without --dry-run to remove them.');
            return self::SUCCESS;
        }

        if (! $this->confirm(sprintf('Soft-delete these %d word%s?', count($invalid), count($invalid) === 1 ? '' : 's'), true)) {
            $this->line('Aborted.');
            return self::SUCCESS;
        }

        foreach ($invalid as $word) {
            $word->delete();
            $this->line("  <info>✓</info> Soft-deleted '{$word->word}'");
        }

        $this->line('');
        $this->info('Audit complete. Historical game data for removed words is preserved.');
        return self::SUCCESS;
    }
}
