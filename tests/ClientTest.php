<?php

namespace rcrowe\Raven\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use rcrowe\Raven\Client;
use Raven_Compat;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testExtendsRavenClient()
    {
        $this->assertInstanceOf('Raven_Client', new Client);
    }

    public function testVersionNumber()
    {
        $this->assertRegExp('/([0-9]+\.[0-9]+\.[0-9]+)/', Client::VERSION);
    }

    public function testDefaultHandler()
    {
        $client = new Client;

        $this->assertInstanceOf('rcrowe\Raven\Handler\Sync', $client->getHandler());
    }

    public function testSetHandler()
    {
        $handler = m::mock('rcrowe\Raven\Handler\HandlerInterface');
        $handler->shouldReceive('foo')->andReturn('bar');

        $client = new Client;
        $client->setHandler($handler);

        $this->assertEquals('bar', $client->getHandler()->foo());
    }

    public function testGetEncoder()
    {
        $client = new Client;

        $this->assertTrue(is_callable($client->getEncoder()));
    }

    public function testEncode()
    {
        $client = new Client;
        $data   = array('foo' => 'bar');

        if (function_exists('gzcompress')) {
            $this->assertEquals(call_user_func($client->getEncoder(), $data), 'eJyrVkrLz1eyUkpKLFKqBQAdegQ0');
        } else {
            $this->assertEquals(Raven_Compat::json_encode($data), '{"foo":"bar"}');
            $this->markTestIncomplete('function `gzcompress` does not exist');
        }
    }

    public function testSetEncoder()
    {
        $client = new Client;
        $client->setEncoder(function() {
            return 'FooBar';
        });

        $this->assertEquals(call_user_func($client->getEncoder(), array()), 'FooBar');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoServers()
    {
        $client = new Client;
        $client->send(array());
    }

    public function testSent()
    {
        $client = new Client('https://123:456@app.getsentry.com/789');
        $data   = array(
            'foo' => 'bar'
        );

        $handler = m::mock('rcrowe\Raven\Handler\HandlerInterface');
        $handler->shouldReceive('process')->once();

        $client->setHandler($handler);
        $this->assertTrue($client->send($data));
    }

    public function testProcessParams()
    {
        $client = new Client('https://123:456@app.getsentry.com/789');
        $data   = array(
            'foo' => 'bar'
        );

        $message = Raven_Compat::json_encode($data);
        if (function_exists("gzcompress")) {
            $message = base64_encode(gzcompress($message));
        }

        $handler = m::mock('rcrowe\Raven\Handler\HandlerInterface');
        $handler->shouldReceive('process')->once()->with(
            'https://app.getsentry.com/api/store/',
            $message,
            m::on(function($param) {
                if (!array_key_exists('User-Agent', $param)) {
                    return false;
                }

                if ($param['User-Agent'] !== 'rcrowe-raven/'.Client::VERSION) {
                    return false;
                }

                if (!array_key_exists('X-Sentry-Auth', $param)) {
                    return false;
                }

                if (!array_key_exists('Content-Type', $param)) {
                    return false;
                }

                if ($param['Content-Type'] !== 'application/octet-stream') {
                    return false;
                }

                return true;
            })
        );

        $client->setHandler($handler);
        $client->send($data);
    }
}
