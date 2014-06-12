<?php

/**
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Handler;

use rcrowe\Raven\Transport\TransportInterface;
use rcrowe\Raven\Client;

/**
 * Handler interface.
 *
 * Handlers deal with a new capture by the Raven client.
 */
interface HandlerInterface
{
    /**
     * Get transport.
     *
     * @return \rcrowe\Raven\Transport\TransportInterface
     */
    public function getTransport();

    /**
     * Set transport.
     *
     * @param \rcrowe\Raven\Transport\TransportInterface $transport
     *
     * @return void
     */
    public function setTransport(TransportInterface $transport);

    /**
     * Process a new Raven message.
     *
     * @param string $url     URL to send data to.
     * @param mixed  $data    Data to you want to send to Sentry.
     * @param array  $headers Headers to send with the request.
     *
     * @return void
     */
    public function process($url, $data, array $headers = []);
}
