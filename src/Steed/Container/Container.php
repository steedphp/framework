<?php

namespace Steed\Container;

use ReflectionClass;
use Steed\Contracts\Container as ContainerContracts;
use Steed\Exception\BindingResolutionException;

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
    protected $instances = [];

    protected $bindings = [];


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

    /**
     * 绑定到容器中
     *
     * @param string $abstract
     * @param string|null $concrete
     * @param bool $shared 是否单例
     */
    public function bind(string $abstract, string $concrete = null, $shared = true): void
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
        return;
    }

    /**
     * 获取真实标识
     *
     * @param $abstract
     * @return mixed
     */
    public function getConcrete($abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }

        return $abstract;
    }

    /**
     * 绑定单例模式
     * @param string $abstract
     * @param null $concrete
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    public function get($abstract)
    {

    }

    public function has($abstract)
    {

    }

    public function make($abstract, $parameters = [])
    {

    }

    public function build($concrete)
    {
        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            return $this->notInstantiable($concrete);
        }

        return $reflector->newInstanceArgs();
    }

    /**
     * Throw an exception that the concrete is not instantiable.
     *
     * @param $concrete
     * @throws BindingResolutionException
     */
    protected function notInstantiable($concrete)
    {
        $message = "Target [$concrete] is not instantiable.";
        throw new BindingResolutionException($message);
    }

}