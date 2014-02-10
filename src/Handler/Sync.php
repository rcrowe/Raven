<?php

/**
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Handler;

use rcrowe\Raven\Client;

/**
 * Default handler.
 *
 * Messages are sent straight away, no background magic. Works just
 * like the default Raven client.
 */
class Sync extends BaseHandler
{
    /**
     * {@inheritdoc}
     */
    public function process($url, $data, array $headers = array())
    {
        $this->transport->send($url, $data, $headers);
    }
}
