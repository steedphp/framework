<?php

namespace Steed\Framework\Container;

use Steed\Framework\Contracts\Container\Container as ContainerContracts;
use Steed\Framework\Exception\ContainerExceptionInterface;
use Steed\Framework\Exception\NotFoundExceptionInterface;
use ReflectionClass;

/**
 * Class Container
 * @package Steed\Framework\Container
 */
class Container implements ContainerContracts
{

    /**
     * Container instance
     * @var
     */
    protected static $instance;

    /**
     * 依赖关系
     * @var array
     */
    protected $dependencies = [];

    /**
     * $instances
     * @var
     */
    protected $instances = [];

    /**
     * get container instance
     * @return ContainerContracts
     */
    public static function getInstance() : ContainerContracts
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     */
    public function get($id)
    {
        try {
            return $this->make($id);
        } catch (ContainerExceptionInterface $bindingResolutionException) {
            throw new NotFoundExceptionInterface($id);
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
     * @throws ContainerExceptionInterface
     */
    protected function notInstantiable($concrete)
    {
        $message = "Target [$concrete] is not instantiable.";
        throw new ContainerExceptionInterface($message);
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        return array_keys($this->instances);
    }

}
