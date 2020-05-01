<?php

namespace Steed\Container;

use ReflectionClass;
use Steed\Contracts\Container\Container as ContainerContracts;
use Steed\Exception\BindingResolutionException;
use Steed\Exception\EntryNotFoundException;
use Steed\Exception\InvalidArgumentException;

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

    private function __construct()
    {
    }

    /**
     * 获取当前容器的实例（单例）
     *
     * @access public
     * @return static
     */
    public static function getInstance(): ContainerContracts
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function get($abstract)
    {
        try {
            return $this->make($abstract);
        } catch (BindingResolutionException $bindingResolutionException) {
            throw new EntryNotFoundException($abstract);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param $abstract
     * @return bool
     */
    public function has($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf('The name parameter must be of type string, %s given',
                is_object($name) ? get_class($name) : gettype($name)));
        }

        if (array_key_exists($name, $this->instances)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 创建实例
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed|object|void
     */
    public function make(string $abstract, array $parameters = [])
    {

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $object = $this->build($abstract);
        $this->instances[$abstract] = $object;

        return $object;
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

    /**
     * @return array
     */
    public function getNames(): array
    {
        return array_keys($this->instances);
    }

}
