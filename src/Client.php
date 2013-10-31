<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven;

use rcrowe\Raven\Handler\HandlerInterface;
use rcrowe\Raven\Handler\Sync;
use Raven_Client;
use Raven_Compat;

/**
 * Raven PHP client
 */
class Client extends Raven_Client
{
    /**
     * @var string Client semver.
     */
    const VERSION = '0.1.0';

    /**
     * @var array Background handlers that implement \rcrowe\Raven\Handler\HandlerInterface.
     */
    protected $handlers = array();

    /**
     * Build the auth header sent to Sentry.
     *
     * @param float  $timestamp  Micro timestamp.
     * @param string $client     Client name/version header.
     * @param string $api_key    Sentry API key.
     * @param string $secret_key Sentry API secret.
     *
     * @return string
     */
    public function getAuthHeader($timestamp, $client, $api_key, $secret_key)
    {
        return $this->get_auth_header($timestamp, $client, $api_key, $secret_key);
    }

    /**
     * Add a background handler.
     *
     * @param \rcrowe\Raven\Handler\HandlerInterface $handler
     *
     * @return void
     */
    public function addHandler(HandlerInterface $handler)
    {
        if (!is_array($handler)) {
            $handler = array($handler);
        }

        $this->handlers = array_merge($this->handlers, $handler);
    }

    /**
     * Get the registered handlers.
     *
     * If no handlers are registered then it defaults to the
     * sync handler (\rcrowe\Raven\Handler\Sync).
     *
     * @return array
     */
    public function getHandlers()
    {
        if (!empty($this->handlers)) {
            return $this->handlers;
        }

        // No handlers set, fallback to sync
        return [
            new Sync
        ];
    }

    /**
     * Return the headers needed for the Sentry API.
     *
     * @param \rcrowe\Raven\Client $client
     *
     * @return array
     */
    public static function getHeaders(Client $client)
    {
        $client_string = 'rcrowe-raven/'.static::VERSION;
        $timestamp     = microtime(true);

        return array(
            'User-Agent'    => $client_string,
            'X-Sentry-Auth' => $client->getAuthHeader(
                $timestamp,
                $client_string,
                $client->public_key,
                $client->secret_key
            ),
            'Content-Type' => 'application/octet-stream',
        );
    }

    /**
     * Get the client options to be serialized.
     *
     * Currently Client::processors is unsupported.
     *
     * @param \rcrowe\Raven\Client $client
     *
     * @return array
     */
    public static function getClientOptions(Client $client)
    {
        return array(
            'logger'          => $client->logger,
            'servers'         => $client->servers,
            'secret_key'      => $client->secret_key,
            'public_key'      => $client->public_key,
            'project'         => $client->project,
            'auto_log_stacks' => $client->auto_log_stacks,
            'name'            => $client->name,
            'site'            => $client->site,
            'tags'            => $client->tags,
            'trace'           => $client->trace,
            'timeout'         => $client->timeout,
            'exclude'         => $client->exclude,
            'shift_vars'      => $client->shift_vars,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function send($data)
    {
        foreach ($this->getHandlers() as $handler) {
            $handler->process($this, $data);
        }
    }
}
