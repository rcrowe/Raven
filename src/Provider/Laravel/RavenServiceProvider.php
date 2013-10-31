<?php

/**
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Provider\Laravel;

use Illuminate\Support\ServiceProvider;
use rcrowe\Raven\Client;
use rcrowe\Raven\Handler\Laravel;

/**
 * Laravel service provider.
 *
 * Integrates with Laravel so you can call Log::error($exception) for example.
 */
class RavenServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $app = $this->app;

        // Register config
        $this->app->config->package('rcrowe/raven', __DIR__.'/config');

        // Bind laravel handler
        $this->app->bindIf('log.sentry.handler', function() use ($app) {
            return new Laravel(null, $app['queue']);
        });

        // Bind raven client
        $this->app->bind('log.sentry', function() use ($app) {

            $client = new Client($app->config->get('raven::dsn'));
            $client->addHandler($app->make('log.sentry.handler'));

            return $client;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $raven = $this->app->make('log.sentry');

        $this->app->log->listen(function($level, $message, $context) use ($raven) {
            if (is_a($message, 'Exception')) {
                $raven->captureException($message);
            } else {
                $raven->captureMessage($message);
            }
        });
    }
}
