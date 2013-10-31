<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Handler;

use rcrowe\Raven\Client;

/**
 * Sends the message to Sentry straight away.
 */
class Sync extends BaseHandler
{
    /**
     * {@inheritdoc}
     */
    public function process(Client $client, array $data)
    {
        $message = $this->encodeMessage($data);

        foreach ($client->servers as $url) {
            $this->getTransport()->send($url, $message, Client::getHeaders($client));
        }
    }
}