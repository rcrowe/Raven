<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Transport;

use GuzzleHttp\ClientInterface as HttpInterface;
use GuzzleHttp\Client as Http;

/**
 * Transport message to Sentry over HTTP.
 */
class Guzzle extends BaseTransport
{
    /**
     * @var \Guzzle\Http\ClientInterface
     */
    protected $http;

    /**
     * Get a new instance.
     *
     * @param array                        $options
     * @param \Guzzle\Http\ClientInterface $http    HTTP transport layer. If none falls back to Guzzle.
     */
    public function __construct(array $options = [], HttpInterface $http = null)
    {
        parent::__construct($options);

        $this->http = (empty($http)) ? new Http : $http;
    }

    /**
     * Get HTTP.
     *
     * @return \Guzzle\Http\ClientInterface
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * Set HTTP.
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
     *
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function send($url, $message, array $headers = [])
    {
        $this->http->post($url, [
            'headers' => $headers,
            'body'    => $message,
        ]);
    }
}
