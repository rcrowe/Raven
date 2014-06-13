<?php

namespace rcrowe\Raven\Tests\Provider\Laravel\Facade;

use rcrowe\Raven\Tests\Provider\Laravel\Base;
use rcrowe\Raven\Provider\Laravel\RavenServiceProvider;
use rcrowe\Raven\Provider\Laravel\Facade\Raven;
use rcrowe\Raven\Client;

class RavenTest extends Base
{
    public function testFacadeInstance()
    {
        $this->bootApplication();

        $this->assertInstanceOf('rcrowe\Raven\Client', Raven::getFacadeRoot());

        $data = Raven::get_default_data();
        $this->assertEquals($data['logger'], 'rcrowe-raven/'.Client::VERSION);
    }

    public function testSetRemoveUser()
    {
        $app  = $this->bootApplication();
        $user = [
            'id'   => 1,
            'name' => 'Rob Crowe',
        ];

        Raven::setUser($user);
        $this->assertEquals($app['log.raven']->context->user, $user);

        Raven::removeUser();
        $this->assertEquals($app['log.raven']->context->user, []);
    }

    protected function bootApplication($enabled = true)
    {
        $app = $this->getApplication($enabled);

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        Raven::setFacadeApplication($app);

        return $app;
    }
}
