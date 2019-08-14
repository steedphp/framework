<?php

namespace Steed\Contracts\Swoole;

use Swoole\Server;

interface SwooleManager
{

    public function createSwooleServer(): Server;

    public function getSwooleServer(): Server;
}