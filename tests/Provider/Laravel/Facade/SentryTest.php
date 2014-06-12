<?php

namespace rcrowe\Raven\Tests\Provider\Laravel\Facade;

use PHPUnit_Framework_TestCase;
use Mockery as m;

class SentryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testTemp()
    {

    }

    // public function testGetSentry()
    // {
    //     $log = new Log(new Logger('test'));
    //     $this->assertNull($log->getSentry());

    //     $client = new Client('http://123:456@foo.com/789');
    //     $log->setSentry($client);

    //     // rcrowe\Raven\Client

    //     $this->assertInstanceOf('rcrowe\Raven\Client', $log->getSentry());
    // }

    // public function testSetUser()
    // {
    //     $log  = new Log(new Logger('test'));
    //     $user = array('username' => 'rcrowe');

    //     $this->assertFalse($log->setUser($user));

    //     $client = new Client('http://123:456@foo.com/789');
    //     $log->setSentry($client);

    //     $this->assertTrue($log->setUser($user));
    //     $this->assertEquals($log->getSentry()->context->user, $user);
    // }

    // public function testRemoveUser()
    // {
    //     $log = new Log(new Logger('test'));

    //     $this->assertFalse($log->removeUser());

    //     $client = new Client('http://123:456@foo.com/789');
    //     $log->setSentry($client);

    //     $this->assertTrue($log->removeUser());
    //     $this->assertEquals($log->getSentry()->context->user, array());
    // }
}
