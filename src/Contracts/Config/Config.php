<?php

namespace Steed\Framework\Contracts\Config;

interface Config
{

    public function has(string $key): bool;

    public function get(string $key, $default);

    public function set(string $key, $value = null);


}
