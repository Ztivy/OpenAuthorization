<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    private const SUPPORTED_PROVIDERS = ['discord', 'twitch'];

    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)
            ->scopes($this->getScopesFor($provider))
            ->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            return redirect()->route('login')
                ->with('error', "No se pudo autenticar con {$provider}. Intenta de nuevo.");
        }

        $user = User::updateOrCreate(
            [
                'provider'    => $provider,
                'provider_id' => (string) $socialUser->getId(),
            ],
            [
                'name'          => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario',
                'email'         => $socialUser->getEmail() ?? null,
                'avatar'        => $socialUser->getAvatar() ?? null,
                'access_token'  => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken ?? null,
                'token_expires' => $socialUser->expiresIn
                    ? now()->addSeconds($socialUser->expiresIn)
                    : null,
            ]
        );

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function validateProvider(string $provider): void
    {
        if (! in_array($provider, self::SUPPORTED_PROVIDERS, strict: true)) {
            abort(404, "Proveedor OAuth '{$provider}' no soportado.");
        }
    }

    private function getScopesFor(string $provider): array
    {
        return match ($provider) {
            'discord' => ['identify', 'email'],
            'twitch'  => ['user:read:email'],
            default   => [],
        };
    }
}