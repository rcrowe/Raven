<?php

namespace rcrowe\Raven\Tests\Transport;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use rcrowe\Raven\Transport\Guzzle;

class GuzzleTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testInstance()
    {
        $transport = new Guzzle;

        $this->assertInstanceOf('rcrowe\Raven\Transport\TransportInterface', $transport);
        $this->assertInstanceOf('rcrowe\Raven\Transport\BaseTransport', $transport);
    }

    public function testDefaultHttp()
    {
        $transport = new Guzzle;
        $http      = $transport->getHttp();

        $this->assertInstanceOf('Guzzle\Http\ClientInterface', $http);
        $this->assertInstanceOf('Guzzle\Http\Client', $http);
    }

    public function testSetHttp()
    {
        $http = m::mock('Guzzle\Http\ClientInterface');
        $http->shouldReceive('foo')->andReturn('bar');

        $transport = new Guzzle(array(), $http);
        $this->assertEquals($transport->getHttp()->foo(), 'bar');

        $http->shouldReceive('throw')->andReturn('poop');
        $transport->setHttp($http);

        $this->assertEquals($transport->getHttp()->throw(), 'poop');
    }

    public function testSend()
    {
        $url     = 'http://foo.com';
        $message = 'hello world';
        $headers = array('foo' => 'bar');

        $entity = m::mock('Guzzle\Http\Message\EntityEnclosingRequestInterface');
        $entity->shouldReceive('send')->once();

        $http = m::mock('Guzzle\Http\ClientInterface');
        $http->shouldReceive('post')->with($url, $headers, $message)->once()->andReturn($entity);

        $transport = new Guzzle(array(), $http);
        $transport->send($url, $message, $headers);
    }
}
