<?php

namespace rcrowe\Raven\Tests\Handler;

use PHPUnit_Framework_TestCase;
use rcrowe\Raven\Handler\BaseHandler;
use rcrowe\Raven\Transport\Dummy;

class TempHandler extends BaseHandler
{
    public function process($url, $data, array $headers = array())
    {
        return true;
    }
}

class BaseHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $handler = new TempHandler;

        $this->assertInstanceOf('rcrowe\Raven\Handler\HandlerInterface', $handler);
    }

    public function testDefaultTransport()
    {
        $handler = new TempHandler;

        $this->assertInstanceOf('rcrowe\Raven\Transport\Guzzle', $handler->getTransport());
    }

    public function testNewInstanceTransport()
    {
        $handler = new TempHandler(new Dummy);

        $this->assertInstanceOf('rcrowe\Raven\Transport\Dummy', $handler->getTransport());
    }

    public function testSetTransport()
    {
        $handler = new TempHandler;
        $handler->setTransport(new Dummy);

        $this->assertInstanceOf('rcrowe\Raven\Transport\Dummy', $handler->getTransport());
    }
}
