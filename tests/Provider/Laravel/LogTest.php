<?php

namespace rcrowe\Raven\Tests\Provider\Laravel;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Exception;
use rcrowe\Raven\Provider\Laravel\Log;
use Monolog\Logger;
use rcrowe\Raven\Client;
use Monolog\Handler\RavenHandler;

class LogTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testGetSentry()
    {
        $log = new Log(new Logger('test'));
        $this->assertNull($log->getSentry());

        $client = new Client('http://123:456@foo.com/789');
        $log->setSentry($client);

        // rcrowe\Raven\Client

        $this->assertInstanceOf('rcrowe\Raven\Client', $log->getSentry());
    }

    public function testSetUser()
    {
        $log  = new Log(new Logger('test'));
        $user = array('username' => 'rcrowe');

        $this->assertFalse($log->setUser($user));

        $client = new Client('http://123:456@foo.com/789');
        $log->setSentry($client);

        $this->assertTrue($log->setUser($user));
        $this->assertEquals($log->getSentry()->context->user, $user);
    }

    public function testRemoveUser()
    {
        $log = new Log(new Logger('test'));

        $this->assertFalse($log->removeUser());

        $client = new Client('http://123:456@foo.com/789');
        $log->setSentry($client);

        $this->assertTrue($log->removeUser());
        $this->assertEquals($log->getSentry()->context->user, array());
    }

    public function testRegisterHandler()
    {
        $log = new Log(new Logger('test'));

        try {
            $log->getMonolog()->popHandler();
            $this->assertFalse(true);
        } catch (Exception $ex) {
            $this->assertTrue(true);
        }

        $log->registerHandler('error', function($level) {
            return new RavenHandler(new Client('http://123:456@foo.com/789'), $level);
        });

        $handler = $log->getMonolog()->popHandler();

        $this->assertInstanceOf('Monolog\Handler\RavenHandler', $handler);
        $this->assertEquals($handler->getLevel(), Logger::ERROR);
    }
}
