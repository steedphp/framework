<?php

namespace Steed\Container;

use Steed\Contracts\Container as ContainerContracts;

class Container implements ContainerContracts
{

    /**
     * Container instance
     * @var
     */
    protected static $instance;

    /**
     * $instances
     * @var
     */
    protected static $instances;


    private function __construct()
    {
    }

    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function singleton($abstract, $concrete = null)
    {

    }

    public function get($abstract)
    {

    }

    public function has($abstract)
    {

    }

}