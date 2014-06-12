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
 * Extended by certain transports.
 */
abstract class BaseTransport implements TransportInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            'class'   => get_class($this),
            'options' => $this->options,
        );
    }
}
