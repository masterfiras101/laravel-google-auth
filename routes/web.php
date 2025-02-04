<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');



use App\Http\Controllers\Socialite\GoogleAuthController;
/**
 * Routes for Google authentication using Laravel Socialite.
 * 
 * Includes routes for:
 * 1. Redirecting to Google authentication (`getGoogleAuth`).
 * 2. Handling the callback from Google (`getGoogleAuthCallback`).
 * 3. Displaying success after a successful login (`getGoogleAuthSuccess`).
 */

Route::prefix('laravel-google-auth')->group(function () {
    Route::controller(GoogleAuthController::class)->group(function () {
        Route::get('/', 'getGoogleAuth')
            ->name('auth.google');
        Route::get('callback', 'getGoogleAuthCallback')
            ->name('auth.google.callback');
        Route::get('success', 'getGoogleAuthSuccess')
            ->name('auth.google.success');
    });
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
