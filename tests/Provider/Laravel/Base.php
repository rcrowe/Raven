<?php

namespace rcrowe\Raven\Tests\Provider\Laravel;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Illuminate\Queue\QueueManager;
use Illuminate\Config\Repository;
use rcrowe\Raven\Provider\Laravel\Log;
use Monolog\Logger;

class Base extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    protected function getApplication($enabled = false)
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
        $config->getLoader()->shouldReceive('exists')->with('monolog', 'raven')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->with('production', 'config', 'raven')->andReturn(
            [
                'dsn'     => 'http://123:456@foo.com/789',
                'enabled' => $enabled,
                'level'   => 'critical',
                'monolog' => [
                    'processors' => [],
                ],
            ]
        );
        $app['config'] = $config;

        $logger = new Log(new Logger('test'));
        $app['log'] = $logger;

        return $app;
    }
}
