<?php

namespace rcrowe\Raven\Tests\Handler\Laravel;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use rcrowe\Raven\Handler\Laravel\Job;

class JobTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testTransportInstance()
    {
        $job  = new Job;
        $data = $this->getData();

        $job->fire($this->getIlluminateJob(), $data);

        $transport = $job->getTransport();

        $this->assertInstanceOf('rcrowe\Raven\Transport\Dummy', $transport);
        $this->assertEquals($data['transport']['options'], $transport->getOptions());
    }

    public function testTransportSent()
    {
        $job  = new Job;
        $data = $this->getData();

        $transport = m::mock('rcrowe\Raven\Transport\TransportInterface');
        $transport->shouldReceive('send')->once()->with($data['url'], $data['data'], $data['headers']);

        $job->setTransport($transport);

        $job->fire($this->getIlluminateJob(), $data);
    }

    protected function getIlluminateJob()
    {
        $illuminate = m::mock('Illuminate\Queue\Jobs\Job');
        $illuminate->shouldReceive('delete')->once();

        return $illuminate;
    }

    protected function getData()
    {
        return [
            'url'     => 'http://foo.com',
            'data'    => 'hello world',
            'headers' => [
                'foo' => 'bar',
            ],
            'transport' => [
                'class'   => 'rcrowe\Raven\Transport\Dummy',
                'options' => [
                    'throw' => 'poop',
                ],
            ],
        ];
    }
}
