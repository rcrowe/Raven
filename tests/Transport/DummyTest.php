<?php

namespace rcrowe\Raven\Tests\Transport;

use PHPUnit_Framework_TestCase;
use rcrowe\Raven\Transport\Dummy;

class DummyTest extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $transport = new Dummy;

        $this->assertInstanceOf('rcrowe\Raven\Transport\TransportInterface', $transport);
        $this->assertInstanceOf('rcrowe\Raven\Transport\BaseTransport', $transport);
    }

    public function testToArray()
    {
        $transport = new Dummy;
        $data      = $transport->toArray();

        $this->assertEquals('rcrowe\Raven\Transport\Dummy', $data['class']);
    }

    public function testAlwaysSends()
    {
        $transport = new Dummy;

        $this->assertTrue($transport->send(null, null));
    }
}
