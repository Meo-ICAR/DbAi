<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;

class SocialiteServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(SocialiteFactory::class, function ($app) {
            return $app['Laravel\\Socialite\\Contracts\\Factory'];
        });
    }

    public function boot()
    {
        $socialite = $this->app->make(SocialiteFactory::class);

        $socialite->extend('microsoft', function ($app) use ($socialite) {
            $config = $app['config']['services.microsoft'];
            
            return $socialite->buildProvider(
                \SocialiteProviders\Microsoft\MicrosoftProvider::class,
                $config
            );
        });

        // Register the Microsoft event
        $this->app->make('events')->listen(
            SocialiteWasCalled::class,
            MicrosoftExtendSocialite::class
        );
    }
}
