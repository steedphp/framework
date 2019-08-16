<?php


namespace Steed\Contracts\Config;


interface Config
{

    public function has($key);

    public function get($key);

    public function set($key, $value = null);


}