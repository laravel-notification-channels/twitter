<?php

namespace NotificationChannels\Twitter;

use Illuminate\Support\ServiceProvider;


class TwitterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // Bootstrap code here.
        $this->app->when(TwitterChannel::class)
            ->needs(Twitter::class)
            ->give(function () {
                return new Twitter(config('services.twitter'));
            });
    }
}
