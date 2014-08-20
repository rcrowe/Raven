<?php

/*
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Handler;

use rcrowe\Raven\Transport\TransportInterface;
use Illuminate\Queue\QueueManager;
use RuntimeException;

/**
 * Uses the Laravel queue to store messages.
 */
class Laravel extends BaseHandler
{
    /**
     * @var \Illuminate\Queue\QueueManager
     */
    protected $queue;

    /**
     * @var String
     */
    protected $customQueue;

    /**
     * New instance.
     *
     * @param \rcrowe\Raven\Transport\TransportInterface $transport
     * @param \Illuminate\Queue\QueueManager             $queue
     */
    public function __construct(TransportInterface $transport = null, QueueManager $queue = null, $customQueue = null)
    {
        parent::__construct($transport);

        $this->queue = $queue;
        $this->customQueue = $customQueue;
    }

    /**
     * Get the queue.
     *
     * @return \Illuminate\Queue\QueueManager
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Set the queue.
     *
     * @param \Illuminate\Queue\QueueManager $queue
     *
     * @return void
     */
    public function setQueue(QueueManager $queue)
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException Thrown if the queue has not been set.
     */
    public function process($url, $data, array $headers = [])
    {
        if (empty($this->queue)) {
            throw new RuntimeException('Queue not set');
        }

        $data = [
            'url'       => $url,
            'data'      => $data,
            'headers'   => $headers,
            'transport' => $this->transport->toArray(),
        ];

        $this->queue->push('rcrowe\Raven\Handler\Laravel\Job', $data, $this->customQueue);
    }
}
