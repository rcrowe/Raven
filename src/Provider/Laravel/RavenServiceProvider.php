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
use InvalidArgumentException;

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
        $this->publishes([realpath(__DIR__.'/../../config/config.php') => config_path('raven.php')]);

        $this->app->bindIf('log.raven.dsn', function () {
            return ($this->app->config->get('services.raven.dsn')) ?: $this->app->config->get('raven.dsn');
        });

        $this->app->bindIf('log.raven.transport', function () {
            return new Transport;
        });

        $this->app->bindIf('log.raven.handler', function () {
            return new Handler(
                $this->app['log.raven.transport'],
                $this->app->queue
            );
        });

        $this->app->bindIf('log.raven.processors', function () {
            return $this->app->config->get('raven.monolog.processors', []);
        });

        $this->app->singleton('log.raven', function () {
            $client = new Client($this->app['log.raven.dsn']);
            $client->tags_context([
                'laravel_environment' => $this->app->environment(),
                'laravel_version'     => Application::VERSION,
            ]);
            $client->setHandler($this->app['log.raven.handler']);

            return $client;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (!$this->app->config->get('raven.enabled')) {
            return;
        }

        $this->app->log = new Log($this->app->log->getMonolog());

        $this->app->log->registerHandler(
            $this->app->config->get('raven.level', 'error'),
            function ($level) {
                $handler = new RavenHandler($this->app['log.raven'], $level);

                // Add processors
                $processors = $this->app['log.raven.processors'];

                if (is_array($processors)) {
                    foreach ($processors as $process) {
                        // Get callable
                        if (is_string($process)) {
                            $callable = new $process;
                        } elseif (is_callable($process)) {
                            $callable = $process;
                        } else {
                            throw new InvalidArgumentException('Raven: Invalid processor');
                        }

                        // Add processor to Raven handler
                        $handler->pushProcessor($callable);
                    }
                }

                return $handler;
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'log.raven',
            'log.raven.handler',
            'log.raven.processors',
            'log.raven.transport',
        ];
    }
}
