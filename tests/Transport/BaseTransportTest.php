<?php

namespace rcrowe\Raven\Tests\Transport;

use PHPUnit_Framework_TestCase;
use rcrowe\Raven\Transport\BaseTransport;

class TempTransport extends BaseTransport
{
    public function send($url, $message, array $headers = array())
    {
        return true;
    }
}

class BaseTransportTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyOptions()
    {
        $transport = new TempTransport;

        $this->assertCount(0 , $transport->getOptions());
    }

    public function testSetOptions()
    {
        $options = array(
            'foo' => 'bar'
        );

        $transport = new TempTransport($options);

        $this->assertArrayHasKey('foo', $transport->getOptions());
    }

    public function testTransportDetails()
    {
        // No options
        $transport = new TempTransport;
        $data      = $transport->toArray();

        $this->assertArrayHasKey('class', $data);
        $this->assertArrayHasKey('options', $data);

        // With options
        $transport = new TempTransport(array('foo' => 'bar'));
        $data      = $transport->toArray();

        $this->assertArrayHasKey('class', $data);
        $this->assertArrayHasKey('options', $data);

        $this->assertEquals($data['class'], 'rcrowe\Raven\Tests\Transport\TempTransport');

        $this->assertArrayHasKey('foo', $data['options']);
        $this->assertEquals('bar', $data['options']['foo']);
    }
}
