<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'show'])->name('game');

Route::middleware('auth')->group(function () {
    Route::post('/game/guess', [GameController::class, 'guess'])->name('game.guess');
    Route::get('/stats', [StatsController::class, 'show'])->name('stats');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/leaderboard', [LeaderboardController::class, 'show'])->name('leaderboard');

require __DIR__.'/auth.php';
