<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven;

use Raven_Client;
use Raven_Compat;
use Closure;
use rcrowe\Raven\Handler\HandlerInterface;
use rcrowe\Raven\Handler\Sync;
use RuntimeException;

class Client extends Raven_Client
{
    /**
     * @var string Client semver.
     */
    const VERSION = '0.2.0';

    /**
     * @var \rcrowe\Raven\Handler\HandlerInterface Background handler.
     */
    protected $handler;

    /**
     * @var \Closure Used to compress the JSON.
     */
    protected $encoder;

    /**
     * {@inheritdoc}
     */
    public function __construct($options_or_dsn = null, $options = array())
    {
        parent::__construct($options_or_dsn, $options);

        $this->handler = new Sync;
        $this->encoder = function (array $data) {
            $message = Raven_Compat::json_encode($data);

            if (function_exists('gzcompress')) {
                $message = base64_encode(gzcompress($message));
            }

            return $message;
        };
    }

    /**
     * Get background handler.
     *
     * @return \rcrowe\Raven\Handler\HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set background handler.
     *
     * @param \rcrowe\Raven\Handler\HandlerInterface $handler
     *
     * @return void
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Return closure used to compress data.
     *
     * @return \Closure
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * Set closure used to compress data.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public function setEncoder(Closure $callback)
    {
        $this->encoder = $callback;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException Thrown when no sentry servers have been set.
     */
    public function send($data)
    {
        if (!$this->servers) {
            throw new RuntimeException('No servers set');
        }

        // Encode & compress data
        $message = call_user_func($this->encoder, $data);

        // Build up headers
        $client_string = 'rcrowe-raven/'.static::VERSION;
        $headers       = array(
            'User-Agent'    => $client_string,
            'X-Sentry-Auth' => $this->get_auth_header(
                microtime(true),
                $client_string,
                $this->public_key,
                $this->secret_key
            ),
            'Content-Type' => 'application/octet-stream',
        );

        foreach ($this->servers as $url) {
            $this->handler->process($url, $message, $headers);
        }

        return true;
    }
}
