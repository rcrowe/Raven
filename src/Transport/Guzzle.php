<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Transport;

use Guzzle\Http\ClientInterface as HttpInterface;
use Guzzle\Http\Client as Http;

/**
 * Transport message to Sentry over HTTP.
 */
class Guzzle implements TransportInterface
{
    /**
     * @var \Guzzle\Http\ClientInterface
     */
    protected $http;

    /**
     * New instance.
     *
     * @param \Guzzle\Http\ClientInterface $http
     */
    public function __construct(HttpInterface $http = null)
    {
        if (!empty($http)) {
            $this->setHttp($http);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            'class' => '\rcrowe\Raven\Transport\Guzzle',
        );
    }

    /**
     * Get http client.
     *
     * If none has been set we default to \Guzzle\Http\Client.
     *
     * @return \Guzzle\Http\Client
     */
    public function getHttp()
    {
        if (!empty($this->http)) {
            return $this->http;
        }

        // Provide default
        return new Http;
    }

    /**
     * Set the HTTP client.
     *
     * @param \Guzzle\Http\ClientInterface $http
     *
     * @return void
     */
    public function setHttp(HttpInterface $http)
    {
        $this->http = $http;
    }

    /**
     * {@inheritdoc}
     */
    public function send($url, $message, array $headers = array())
    {
        $this->getHttp()->post($url, $headers, $message)->send();
    }
}
