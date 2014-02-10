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
use rcrowe\Raven\Transport\Guzzle as Transport;
use rcrowe\Raven\Handler\Laravel as Handler;
use rcrowe\Raven\Client;
use Illuminate\Foundation\Application;
use Monolog\Handler\RavenHandler;

/**
 * Adds logging to Sentry (http://getsentry.com) to Laravel.
 *
 * Adds exception logging function `Log::exception()`.
 */
class RavenServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $app = $this->app;

        $app->config->package('rcrowe/raven', __DIR__.'/config');

        $app->bindIf('log.sentry.transport', function () {
            return new Transport;
        });

        $app->bindIf('log.sentry.handler', function () use ($app) {
            return new Handler($app['log.sentry.transport'], $app['queue']);
        });

        $app->singleton('log.sentry', function () use ($app) {
            $client = new Client($app->config->get('raven::dsn'), array(
                'logger' => 'rcrowe-raven/'.Client::VERSION,
            ));
            $client->tags_context(array(
                'laravel_environment' => $app->environment(),
                'laravel_version'     => Application::VERSION,
            ));
            $client->setHandler($app['log.sentry.handler']);

            return $client;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $app = $this->app;

        $app['log'] = new Log($app['log']->getMonolog());
        $app['log']->setExceptionLevel($app->config->get('raven::exceptionLevel', 'error'));

        if (!$app->config->get('raven::enabled')) {
            return;
        }

        $app['log']->setSentry($app['log.sentry']);
        $app['log']->registerHandler(
            $app->config->get('raven::level', 'error'),
            function ($level) use ($app) {
                return new RavenHandler($app['log.sentry'], $level);
            }
        );
    }
}
