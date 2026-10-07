<?php

declare(strict_types=1);

namespace Voybit\PaymentGateway\Laravel;

use Illuminate\Support\ServiceProvider;
use Voybit\PaymentGateway\Client;

final class VoybitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/voybit.php', 'voybit');
        $this->app->singleton(Client::class, function ($app): Client {
            return new Client((string) $app['config']->get('voybit.api_key'));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__) . '/config/voybit.php' => config_path('voybit.php'),
            ], 'voybit-config');
        }
    }
}
