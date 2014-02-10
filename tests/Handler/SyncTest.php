<?php

namespace rcrowe\Raven\Tests\Handler;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use rcrowe\Raven\Handler\Sync;

class SyncTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testInstance()
    {
        $handler = new Sync;

        $this->assertInstanceOf('rcrowe\Raven\Handler\HandlerInterface', $handler);
        $this->assertInstanceOf('rcrowe\Raven\Handler\BaseHandler', $handler);
    }

    public function testProcess()
    {
        $url     = 'http://foo.com';
        $data    = 'bar';
        $headers = array('foo' => 'bar');

        $transport = m::mock('rcrowe\Raven\Transport\TransportInterface');
        $transport->shouldReceive('send')->with($url, $data, $headers)->once();

        $handler = new Sync($transport);
        $handler->process($url, $data, $headers);
    }
}
