<?php

namespace Steed\Contracts\Swoole;

use Swoole\Server;

interface SwooleManager
{
    public function createSwooleServer($port, $type, $address = '0.0.0.0', array $setting = [], ...$args): Server;

    public function getSwooleServer(): Server;

    public function start();
}