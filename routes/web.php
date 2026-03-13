<?php

use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::prefix('auth')->controller(SocialAuthController::class)->group(function () {

    Route::get('/{provider}/redirect', 'redirect')
        ->name('oauth.redirect')
        ->where('provider', 'discord|twitch');

    Route::get('/{provider}/callback', 'callback')
        ->name('oauth.callback')
        ->where('provider', 'discord|twitch');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index', ['user' => auth()->user()]);
    })->name('dashboard');

    Route::post('/logout', [SocialAuthController::class, 'logout'])
        ->name('logout');
});