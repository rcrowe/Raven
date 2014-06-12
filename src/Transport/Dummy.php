<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Transport;

/**
 * Dummy transport so that nothing is actually sent.
 *
 * Usual for example so that nothing is transmitted in a dev environment.
 */
class Dummy extends BaseTransport
{
    /**
     * {@inheritdoc}
     */
    public function send($url, $message, array $headers = [])
    {
        return true;
    }
}
