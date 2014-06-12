<?php

namespace rcrowe\Raven\Tests\Handler;

use PHPUnit_Framework_TestCase;
use rcrowe\Raven\Handler\BaseHandler;
use rcrowe\Raven\Transport\Dummy;
use rcrowe\Raven\Tests\Fixture\TempHandler;

class BaseHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf('rcrowe\Raven\Handler\HandlerInterface', new TempHandler);
    }

    public function testDefaultTransport()
    {
        $this->assertInstanceOf('rcrowe\Raven\Transport\Guzzle', (new TempHandler)->getTransport());
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
