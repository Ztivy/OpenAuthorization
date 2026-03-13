<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * SocialAuthController
 *
 * Maneja el flujo completo de OAuth 2.0 con proveedores externos.
 *
 * FLUJO OAuth 2.0 (Authorization Code Flow):
 *  1. redirect()  → Redirige al Authorization Server del proveedor
 *  2. callback()  → El proveedor regresa con un `code` de autorización
 *  3. Socialite intercambia el `code` por un Access Token (POST al token endpoint)
 *  4. Con el Access Token obtenemos los datos del usuario del Resource Server
 *  5. Creamos/actualizamos el usuario en nuestra DB y lo autenticamos
 */
class SocialAuthController extends Controller
{
    /**
     * Proveedores OAuth 2.0 soportados.
     * Agregar aquí cualquier nuevo proveedor configurado en config/services.php
     */
    private const SUPPORTED_PROVIDERS = ['discord', 'spotify'];

    /**
     * PASO 1 del flujo OAuth 2.0:
     * Genera la Authorization URL y redirige al usuario al proveedor.
     *
     * La URL incluye:
     *  - client_id     : identifica nuestra aplicación
     *  - redirect_uri  : a dónde regresa el usuario tras autorizar
     *  - response_type : "code" (Authorization Code Flow)
     *  - scope         : permisos que solicitamos
     *  - state         : valor aleatorio para prevenir ataques CSRF
     */
    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)
            ->scopes($this->getScopesFor($provider))
            ->redirect();
    }

    /**
     * PASO 2 del flujo OAuth 2.0:
     * El proveedor redirige aquí con el Authorization Code.
     * Socialite lo intercambia automáticamente por un Access Token.
     *
     * @param  string $provider  discord | spotify
     */
    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            // Socialite hace la petición POST al token endpoint del proveedor
            // y luego usa el access_token para obtener el perfil del usuario
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            return redirect()->route('login')
                ->with('error', "No se pudo autenticar con {$provider}. Intenta de nuevo.");
        }

        // Buscar usuario existente por provider + provider_id
        // o crear uno nuevo (upsert)
        $user = User::updateOrCreate(
            [
                'provider'    => $provider,
                'provider_id' => $socialUser->getId(),
            ],
            [
                'name'          => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario',
                'email'         => $socialUser->getEmail(),
                'avatar'        => $socialUser->getAvatar(),
                'access_token'  => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'token_expires' => $socialUser->expiresIn
                    ? now()->addSeconds($socialUser->expiresIn)
                    : null,
            ]
        );

        // Autenticar en la sesión de Laravel
        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    /**
     * Valida que el proveedor solicitado sea soportado.
     */
    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, self::SUPPORTED_PROVIDERS, strict: true)) {
            abort(404, "Proveedor OAuth '{$provider}' no soportado.");
        }
    }

    /**
     * Define los scopes (permisos) que pedimos a cada proveedor.
     *
     * Discord scopes:  identify = datos básicos, email = correo
     * Spotify scopes:  user-read-email = email, user-read-private = perfil
     */
    private function getScopesFor(string $provider): array
    {
        return match ($provider) {
            'discord' => ['identify', 'email'],
            'spotify' => ['user-read-email', 'user-read-private'],
            default   => [],
        };
    }
}