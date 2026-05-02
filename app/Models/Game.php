<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
{
    protected $fillable = [
        'user_id', 'daily_word_id', 'guesses', 'status', 'attempts', 'completed_at',
    ];

    protected $casts = [
        'guesses' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyWord(): BelongsTo
    {
        return $this->belongsTo(DailyWord::class);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['won', 'lost']);
    }

    public function shareText(): string
    {
        $word = $this->dailyWord;
        $lines = [];
        $lines[] = "Wordly #{$word->id} " . ($this->status === 'won' ? $this->attempts . '/6' : 'X/6');
        $lines[] = '';

        foreach ($this->guesses as $guess) {
            $row = '';
            foreach ($guess['result'] as $state) {
                $row .= match ($state) {
                    'correct' => '🟩',
                    'present' => '🟨',
                    default   => '⬛',
                };
            }
            $lines[] = $row;
        }

        $lines[] = '';
        $lines[] = config('app.url');

        return implode("\n", $lines);
    }
}
