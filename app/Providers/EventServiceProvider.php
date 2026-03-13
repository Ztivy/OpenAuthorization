<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Discord\DiscordExtendSocialite;
use SocialiteProviders\Spotify\SpotifyExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SocialiteWasCalled::class => [
            DiscordExtendSocialite::class,
            SpotifyExtendSocialite::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}