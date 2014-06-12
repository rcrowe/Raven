<?php

namespace rcrowe\Raven\Tests\Fixture;

use rcrowe\Raven\Transport\BaseTransport;

class TempTransport extends BaseTransport
{
    public function send($url, $message, array $headers = array())
    {
        return true;
    }
}
