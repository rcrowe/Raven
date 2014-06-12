<?php

namespace rcrowe\Raven\Tests\Fixture;

use rcrowe\Raven\Handler\BaseHandler;

class TempHandler extends BaseHandler
{
    public function process($url, $data, array $headers = [])
    {
        return true;
    }
}
