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
 * Transport interface.
 *
 * Transports are used to send Raven data to the Sentry API.
 */
interface TransportInterface
{
    /**
     * Encode the transport data for the background worker.
     *
     * Can be reused to create a new transport object with the same
     * parameters it was when calling the queue.
     *
     * @return array
     */
    public function toArray();

    /**
     * Send message to Sentry.
     *
     * @todo Return whether message was successfully sent.
     *
     * @param string $url
     * @param string $message
     * @param array  $headers
     *
     * @return void
     */
    public function send($url, $message, array $headers = array());
}
