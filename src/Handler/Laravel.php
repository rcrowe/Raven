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
use rcrowe\Raven\Client;
use Illuminate\Queue\QueueManager;

/**
 * Make use of Laravels queue API.
 */
class Laravel extends BaseHandler
{
    /**
     * @var \Illuminate\Queue\QueueManager
     */
    protected $queue;

    /**
     * New instance.
     *
     * @param \rcrowe\Raven\Transport\TransportInterface $transport
     * @param \Illuminate\Queue\QueueManager             $queue
     */
    public function __construct(TransportInterface $transport = null, QueueManager $queue)
    {
        parent::__construct($transport);

        if (!empty($queue)) {
            $this->setQueue($queue);
        }
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
     */
    public function process(Client $client, array $message)
    {
        $data = array(
            'message'   => $this->encodeMessage($message),
            'client'    => Client::getClientOptions($client),
            'transport' => $this->getTransport()->toArray(),
        );

        $this->queue->push('rcrowe\Raven\Handler\Laravel\Job', $data);
    }
}
