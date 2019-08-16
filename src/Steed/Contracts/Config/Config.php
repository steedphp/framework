<?php


namespace Steed\Contracts\Config;


interface Config
{

    public function has($key): bool;

    public function get($key, $default);

    public function set($key, $value = null);


}