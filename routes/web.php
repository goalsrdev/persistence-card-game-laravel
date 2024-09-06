<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardGameController;

Auth::routes(); 

Route::middleware('auth')->group(function () {
    Route::get('/cardgame', [CardGameController::class, 'index'])->name('cardgame.index');
    Route::post('/cardgame/guess', [CardGameController::class, 'handleGuess'])->name('cardgame.guess');
    Route::post('/cardgame/new', [CardGameController::class, 'newGame'])->name('cardgame.new');
    Route::post('/cardgame/clear', [CardGameController::class, 'clearSession'])->name('cardgame.clear');
});
Route::get('/', function () {
    return redirect('/cardgame');
});