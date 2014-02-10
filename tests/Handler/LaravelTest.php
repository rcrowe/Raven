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
        $handler = new Laravel;

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

        $handler = new Laravel;
        $handler->setQueue($queue);

        $this->assertEquals($handler->getQueue()->hello(), 'world');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testQueueNotSet()
    {
        $handler = new Laravel;
        $handler->process('', '', array());
    }

    public function testProcess()
    {
        $url     = 'https://123:456@app.getsentry.com/789';
        $data    = 'hello world';
        $headers = array('foo' => 'bar');

        $queue = m::mock('Illuminate\Queue\QueueManager');
        $queue->shouldReceive('push')->once()->with('rcrowe\Raven\Handler\Laravel\Job', array(
            'url'       => $url,
            'data'      => $data,
            'headers'   => $headers,
            'transport' => array(
                'class'   => 'rcrowe\Raven\Transport\Guzzle',
                'options' => array(),
            ),
        ));

        $handler = new Laravel(null, $queue);
        $handler->process($url, $data, $headers);
    }
}
