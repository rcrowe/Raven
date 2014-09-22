<?php

namespace rcrowe\Raven\Tests\Handler;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use rcrowe\Raven\Handler\Laravel;

class LaravelTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testInstance()
    {
        $handler = new Laravel();

        $this->assertInstanceOf('rcrowe\Raven\Handler\HandlerInterface', $handler);
        $this->assertInstanceOf('rcrowe\Raven\Handler\BaseHandler', $handler);
    }

    public function testNewInstanceQueue()
    {
        $queue = m::mock('Illuminate\Queue\QueueManager');
        $queue->shouldReceive('foo')->andReturn('bar');

        $handler = new Laravel(null, $queue);

        $this->assertEquals($handler->getQueue()->foo(), 'bar');
    }

    public function testSetQueue()
    {
        $queue = m::mock('Illuminate\Queue\QueueManager');
        $queue->shouldReceive('hello')->andReturn('world');

        $handler = new Laravel();
        $handler->setQueue($queue);

        $this->assertEquals($handler->getQueue()->hello(), 'world');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testQueueNotSet()
    {
        $handler = new Laravel();
        $handler->process('', '', []);
    }

    public function testProcess()
    {
        $url     = 'https://123:456@app.getsentry.com/789';
        $data    = 'hello world';
        $headers = ['foo' => 'bar'];

        $queue = m::mock('Illuminate\Queue\QueueManager');
        $queue->shouldReceive('push')->once()->with('rcrowe\Raven\Handler\Laravel\Job', [
            'url'       => $url,
            'data'      => $data,
            'headers'   => $headers,
            'transport' => [
                'class'   => 'rcrowe\Raven\Transport\Guzzle',
                'options' => [],
            ],
        ], null);

        (new Laravel(null, $queue, null))->process($url, $data, $headers);
    }

    public function testProcessWithQueue()
    {
        $url     = 'https://123:456@app.getsentry.com/789';
        $data    = 'hello world';
        $headers = ['foo' => 'bar'];

        $queue = m::mock('Illuminate\Queue\QueueManager');
        $queue->shouldReceive('push')->once()->with('rcrowe\Raven\Handler\Laravel\Job', [
            'url'       => $url,
            'data'      => $data,
            'headers'   => $headers,
            'transport' => [
                'class'   => 'rcrowe\Raven\Transport\Guzzle',
                'options' => [],
            ],
        ], 'errors');

        (new Laravel(null, $queue, 'errors'))->process($url, $data, $headers);
    }
}
