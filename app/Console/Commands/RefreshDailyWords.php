<?php

namespace App\Console\Commands;

use App\Models\DailyWord;
use Database\Seeders\DailyWordSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('wordly:refresh-words {--days=365 : How many future days to ensure are covered}')]
#[Description('Ensure daily words are seeded for the next N days.')]
class RefreshDailyWords extends Command
{
    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $seeder  = new DailyWordSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->info("Daily words refreshed. Coverage checked for {$days} days.");
        return self::SUCCESS;
    }
}
