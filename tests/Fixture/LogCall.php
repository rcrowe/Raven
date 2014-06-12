<?php

namespace rcrowe\Raven\Tests\Fixture;

use rcrowe\Raven\Provider\Laravel\Log;

class LogCall extends Log
{
    protected function fireLogEvent($level, $message, array $context = array())
    {
        $this->level   = $level;
        $this->message = $message;
        $this->context = $context;
    }
}
