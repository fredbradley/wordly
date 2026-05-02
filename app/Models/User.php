<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'current_streak', 'max_streak', 'games_played', 'games_won',
        'guess_distribution', 'last_played_date', 'last_login_ip', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'last_played_date'  => 'date',
            'password'          => 'hashed',
            'guess_distribution' => 'array',
        ];
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function winRate(): int
    {
        if ($this->games_played === 0) return 0;
        return (int) round(($this->games_won / $this->games_played) * 100);
    }

    public function recordLogin(string $ip): void
    {
        $this->forceFill([
            'last_login_ip' => $ip,
            'last_login_at' => now(),
        ])->save();
    }
}
