<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class DailyWord extends Model
{
    protected $fillable = ['word', 'date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public static function today(): ?self
    {
        return self::whereDate('date', Carbon::today()->toDateString())->first();
    }
}
