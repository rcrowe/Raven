<?php

namespace rcrowe\Raven\Tests\Provider\Laravel;

use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\Log\Writer;
use rcrowe\Raven\Provider\Laravel\RavenServiceProvider;
use rcrowe\Raven\Client;
use Monolog\Logger;

class ProviderTest extends Base
{
    public function testConfigLoaded()
    {
        $app = new Application;

        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldReceive('package')
               ->once()
               ->with('rcrowe/raven', realpath(__DIR__.'/../../../').'/src/Provider/Laravel/config');
        $app['config'] = $config;

        $provider = new RavenServiceProvider($app);
        $provider->register();
    }

    public function testTransportBound()
    {
        $app = $this->getApplication();

        $provider = new RavenServiceProvider($app);
        $provider->register();

        $this->assertTrue($app->bound('log.raven.transport'));
        $this->assertInstanceOf('rcrowe\Raven\Transport\Guzzle', $app->make('log.raven.transport'));
    }

    public function testHandlerBound()
    {
        $app = $this->getApplication();

        $provider = new RavenServiceProvider($app);
        $provider->register();

        $this->assertTrue($app->bound('log.raven.handler'));
        $this->assertInstanceOf('rcrowe\Raven\Handler\Laravel', $app->make('log.raven.handler'));
    }

    public function testClientBound()
    {
        $app = $this->getApplication();

        $provider = new RavenServiceProvider($app);
        $provider->register();

        $client = $app->make('log.raven');

        $this->assertTrue($app->bound('log.raven'));
        $this->assertInstanceOf('rcrowe\Raven\Client', $client);

        $this->assertEquals('rcrowe-raven/'.Client::VERSION, $client->logger);
        $this->assertEquals('http://foo.com/api/store/', $client->servers[0]);
        $this->assertEquals('123', $client->public_key);
        $this->assertEquals('456', $client->secret_key);
        $this->assertEquals('789', $client->project);

        $this->assertEquals('production', $client->context->tags['laravel_environment']);
        $this->assertEquals(Application::VERSION, $client->context->tags['laravel_version']);

        $this->assertInstanceOf('rcrowe\Raven\Handler\Laravel', $client->getHandler());
    }

    public function testDisabled()
    {
        $app        = $this->getApplication();
        $app['log'] = new Writer(new Logger('test'));

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertInstanceOf('Illuminate\Log\Writer', $app['log']);

        try {
            $app['log']->getMonolog()->popHandler();
            $this->assertFalse(true);
        } catch (\LogicException $ex) {
            $this->assertTrue(true);
        }
    }

    public function testLogPatched()
    {
        $app        = $this->getApplication(true);
        $app['log'] = new Writer(new Logger('test'));

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertInstanceOf('rcrowe\Raven\Provider\Laravel\Log', $app['log']);
    }

    public function testRavenHandlerRegistered()
    {
        $app = $this->getApplication(true);

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        $handler = $app['log']->getMonolog()->popHandler();

        $this->assertInstanceOf('Monolog\Handler\RavenHandler', $handler);
        $this->assertEquals($handler->getLevel(), Logger::CRITICAL);
    }

    public function testStringProcessorRegistered()
    {
        $app = $this->getApplication(true);
        $app['log.raven.processors'] = array(
            'Monolog\Processor\GitProcessor'
        );

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertInstanceOf('Monolog\Processor\GitProcessor', $app['log']->getMonolog()->popHandler()->popProcessor());
    }

    public function testClosureProcessorRegistered()
    {
        $app = $this->getApplication(true);
        $app['log.raven.processors'] = array(
            function ($result) {
                var_dump($result);
            }
        );

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        $this->assertInstanceOf('Closure', $app['log']->getMonolog()->popHandler()->popProcessor());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidProcessor()
    {
        $app = $this->getApplication(true);
        $app['log.raven.processors'] = array(
            123
        );

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();
    }
}
