<?php

namespace rcrowe\Raven\Tests\Provider\Laravel\Facade;

use rcrowe\Raven\Tests\Provider\Laravel\Base;
use rcrowe\Raven\Provider\Laravel\RavenServiceProvider;
use rcrowe\Raven\Provider\Laravel\Facade\Sentry;
use rcrowe\Raven\Client;

class SentryTest extends Base
{
    public function testFacadeInstance()
    {
        $this->bootApplication();

        $this->assertInstanceOf('rcrowe\Raven\Client', Sentry::getFacadeRoot());

        $data = Sentry::get_default_data();
        $this->assertEquals($data['logger'], 'rcrowe-raven/'.Client::VERSION);
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
        $app = $this->getApplication($enabled);

        $provider = new RavenServiceProvider($app);
        $provider->register();
        $provider->boot();

        Sentry::setFacadeApplication($app);

        return $app;
    }
}
