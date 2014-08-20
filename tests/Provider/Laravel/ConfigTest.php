<?php

namespace rcrowe\Raven\Tests\Provider\Laravel;

use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testOptions()
    {
        $config = require __DIR__.'/../../../src/config/config.php';

        $this->assertArrayHasKey('dsn', $config);
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('level', $config);
        $this->assertArrayHasKey('queue', $config);
        $this->assertArrayHasKey('connection', $config['queue']);
        $this->assertArrayHasKey('queue', $config['queue']);
        $this->assertArrayHasKey('monolog', $config);
        $this->assertArrayHasKey('processors', $config['monolog']);

        $this->assertTrue(is_string($config['dsn']));
        $this->assertTrue(is_bool($config['enabled']));
        $this->assertTrue(is_string($config['level']));
        $this->assertTrue(is_array($config['queue']));
        $this->assertTrue(is_string($config['queue']['connection']));
        $this->assertTrue(is_string($config['queue']['queue']));
        $this->assertTrue(is_array($config['monolog']));
        $this->assertTrue(is_array($config['monolog']['processors']));
    }
}
