<?php

namespace rcrowe\Raven\Handler\Laravel;

use Illuminate\Queue\Jobs\Job as IlluminateJob;
use rcrowe\Raven\Client;

class Job
{
    public function fire(IlluminateJob $job, $data)
    {
        $client    = new Client($data['client']);
        $transport = new $data['transport']['class'];

        foreach ($client->servers as $url) {
            $transport->send($url, $data['message'], Client::getHeaders($client));
        }

        $job->delete();
    }
}
