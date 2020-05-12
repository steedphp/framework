<?php

namespace Steed\Framework\Contracts\Config;

interface Config
{

    public function has($key): bool;

    public function get($key, $default);

    public function set($key, $value = null);


}
