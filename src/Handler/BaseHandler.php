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
        $this->transport = (empty($transport)) ? new Guzzle : $transport;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
    }
}
