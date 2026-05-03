<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Populate the word bank
        $this->call(DailyWordSeeder::class);

        // Assign the next 30 days from today
        $this->command->call('wordly:assign-daily-word', ['--days' => 30]);
    }
}
