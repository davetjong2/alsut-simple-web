<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SawitController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('sawit.kebun');
    }

    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/kebun', [SawitController::class, 'kebun'])
        ->name('sawit.kebun');
    Route::get('/berkebun', [SawitController::class, 'berkebun'])
        ->name('sawit.berkebun');
    Route::post('/berkebun/tanam', [SawitController::class, 'tanam'])
        ->name('sawit.tanam');
    Route::post('/berkebun/panen', [SawitController::class, 'panen'])
        ->name('sawit.panen');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    Route::delete('/profile-picture', [ProfileController::class, 'deletePicture'])
        ->name('profile.picture.delete');
});

require __DIR__.'/auth.php';