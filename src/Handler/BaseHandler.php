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
use rcrowe\Raven\Transport\Guzzle;
use rcrowe\Raven\Client;
use Raven_Compat;

/**
 * Extended by certain handlers.
 */
abstract class BaseHandler implements HandlerInterface
{
    /**
     * @var \rcrowe\Raven\Transport\TransportInterface
     */
    protected $transport;

    /**
     * New instance.
     *
     * @param \rcrowe\Raven\Transport\TransportInterface $transport
     */
    public function __construct(TransportInterface $transport = null)
    {
        if (!empty($transport)) {
            $this->setTransport($transport);
        }
    }

    /**
     * Get the transport.
     *
     * @return \rcrowe\Raven\Transport\TransportInterface
     */
    public function getTransport()
    {
        if (!empty($this->transport)) {
            return $this->transport;
        }

        // No transport set, use Guzzle as default
        return new Guzzle;
    }

    /**
     * Set transport.
     *
     * @param \rcrowe\Raven\Transport\TransportInterface $transport
     *
     * @return void
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Encode the collected data.
     *
     * @param array $data
     *
     * @return string
     */
    public function encodeMessage(array $data)
    {
        $message = Raven_Compat::json_encode($data);

        if (function_exists("gzcompress")) {
            $message = base64_encode(gzcompress($message));
        }

        return $message;
    }
}
