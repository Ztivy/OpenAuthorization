<?php

use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web - OAuth 2.0 Login
|--------------------------------------------------------------------------
|
| Rutas públicas:
|   GET  /              → Redirige a login o dashboard
|   GET  /login         → Página de inicio de sesión con botones OAuth
|
| Rutas del flujo OAuth 2.0:
|   GET  /auth/{provider}/redirect  → Paso 1: Redirige al Authorization Server
|   GET  /auth/{provider}/callback  → Paso 2: Recibe el Authorization Code
|
| Rutas protegidas (requieren autenticación):
|   GET  /dashboard     → Panel principal del usuario
|   POST /logout        → Cerrar sesión
|
*/

// ── Página de inicio → redirige según estado de autenticación ──────────────
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ── Página de Login ────────────────────────────────────────────────────────
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

// ── Flujo OAuth 2.0 ────────────────────────────────────────────────────────
Route::prefix('auth')->controller(SocialAuthController::class)->group(function () {

    // Paso 1: Genera Authorization URL y redirige al proveedor
    Route::get('/{provider}/redirect', 'redirect')
        ->name('oauth.redirect')
        ->where('provider', 'discord|spotify');

    // Paso 2: El proveedor regresa aquí con el code de autorización
    Route::get('/{provider}/callback', 'callback')
        ->name('oauth.callback')
        ->where('provider', 'discord|spotify');
});

// ── Rutas protegidas ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index', ['user' => auth()->user()]);
    })->name('dashboard');

    Route::post('/logout', [SocialAuthController::class, 'logout'])
        ->name('logout');
});