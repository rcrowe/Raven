<?php

namespace rcrowe\Raven\Tests\Provider\Laravel\Facade;

use PHPUnit_Framework_TestCase;
use Mockery as m;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\Queue\QueueManager;
use rcrowe\Raven\Provider\Laravel\Log;
use Monolog\Logger;
use rcrowe\Raven\Provider\Laravel\RavenServiceProvider;

use rcrowe\Raven\Provider\Laravel\Facade\Sentry;

class SentryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testFacadeInstance()
    {
        $this->bootApplication();

        $this->assertInstanceOf('rcrowe\Raven\Client', Sentry::getFacadeRoot());
    }

    public function testSetRemoveUser()
    {
        $app  = $this->bootApplication();
        $user = array(
            'id'   => 1,
            'name' => 'Rob Crowe',
        );

        Sentry::setUser($user);
        $this->assertEquals($app['log.raven']->context->user, $user);

        Sentry::removeUser();
        $this->assertEquals($app['log.raven']->context->user, array());
    }

    protected function bootApplication($enabled = true)
    {
        $app          = new Application;
        $app['env']   = 'production';
        $app['queue'] = new QueueManager($app);

        $config = new Repository(m::mock('Illuminate\Config\LoaderInterface'), 'production');

        $config->getLoader()->shouldReceive('addNamespace');
        $config->getLoader()->shouldReceive('cascadePackage')
            ->andReturnUsing(function ($env, $package, $group, $items) {
                return $items;
            });
        $config->getLoader()->shouldReceive('exists')->with('environments', 'raven')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('dsn', 'raven')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('enabled', 'raven')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->with('level', 'raven')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->with('production', 'config', 'raven')->andReturn(
            array(
                'dsn'     => 'http://123:456@foo.com/789',
                'enabled' => $enabled,
                'level'   => 'critical',
            )
        );
        $app['config'] = $config;

        $logger = new Log(new Logger('test'));
        $app['log'] = $logger;


        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        Sentry::setFacadeApplication($app);

        return $app;
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
