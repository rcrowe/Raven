<?php

namespace rcrowe\Raven\Tests\Provider\Laravel;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Exception;
use rcrowe\Raven\Provider\Laravel\Log;
use Monolog\Logger;
use rcrowe\Raven\Client;
use Monolog\Handler\RavenHandler;
use Monolog\Handler\NullHandler;
use rcrowe\Raven\Tests\Fixture\LogCall;

class LogTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnknownLogLevel()
    {
        $log = new Log(new Logger('test'));
        $log->foo('test');
    }

    public function testMessageLog()
    {
        $log = new LogCall(new Logger('test'));
        $log->registerHandler('error', function ($level) {
            return new NullHandler($level);
        });

        $this->assertTrue($log->error('hello foo bar'));
        $this->assertEquals('error', $log->level);
        $this->assertEquals('hello foo bar', $log->message);
        $this->assertEquals([], $log->context);
    }

    public function testExceptionLog()
    {
        $log = new LogCall(new Logger('test'));
        $log->registerHandler('error', function ($level) {
            return new NullHandler($level);
        });

        try {
            throw new Exception('foo bar hello world');
        } catch (Exception $ex) {
            $this->assertTrue($log->error($ex));
            $this->assertEquals('error', $log->level);
            $this->assertEquals('foo bar hello world', $log->message);
            $this->assertArrayHasKey('exception', $log->context);
            $this->assertInstanceOf('Exception', $log->context['exception']);
        }

        try {
            throw new Exception('foo bar hello world');
        } catch (Exception $ex) {
            $this->assertTrue($log->error($ex, [
                'foo' => 'bar'
            ]));
            $this->assertEquals('error', $log->level);
            $this->assertEquals('foo bar hello world', $log->message);
            $this->assertArrayHasKey('exception', $log->context);
            $this->assertInstanceOf('Exception', $log->context['exception']);
            $this->assertArrayHasKey('foo', $log->context);
            $this->assertEquals('bar', $log->context['foo']);
        }
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

        $log->registerHandler('error', function ($level) {
            return new RavenHandler(new Client('http://123:456@foo.com/789'), $level);
        });

        $handler = $log->getMonolog()->popHandler();

        $this->assertInstanceOf('Monolog\Handler\RavenHandler', $handler);
        $this->assertEquals($handler->getLevel(), Logger::ERROR);
    }
}
