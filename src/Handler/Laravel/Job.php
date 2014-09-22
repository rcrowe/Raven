<?php

/**
 * This file is part of rcrowe\Raven.
 *
 * This package makes use of the Sentry Raven client (https://github.com/getsentry/raven-php).
 *
 * (c) Rob Crowe <hello@vivalacrowe.com>
 */

namespace rcrowe\Raven\Handler\Laravel;

use Illuminate\Queue\Jobs\Job as IlluminateJob;
use rcrowe\Raven\Transport\TransportInterface;

/**
 * Job that is fired by the Illuminate queue.
 */
class Job
{
    /**
     * @var \rcrowe\Raven\Transport\TransportInterface
     */
    protected $transport;

    /**
     * Get the transport that was used when this job was fired.
     *
     * @return \rcrowe\Raven\Transport\TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Set the transport when firing the job.
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
     * Called by the illuminate queue.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data Data that has been added by the Laravel handler.
     *
     * @return void
     */
    public function fire(IlluminateJob $job, array $data)
    {
        if (empty($this->transport)) {
            $this->transport = new $data['transport']['class']($data['transport']['options']);
        }

        try {
            $this->transport->send($data['url'], $data['data'], $data['headers']);
            $job->delete();
        } catch (\Exception $e) {
            $job->release(30);
        }
    }
}
