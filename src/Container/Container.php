<?php

namespace Steed\Framework\Container;

use Steed\Framework\Contracts\Container\Container as ContainerContracts;
use Steed\Framework\Exception\ContainerExceptionInterface;
use Steed\Framework\Exception\InvalidArgumentException;
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
    public static function getInstance(): ContainerContracts
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
     * {@inheritdoc}
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
            return $this->resolve($id);
        } catch (ContainerExceptionInterface $bindingResolutionException) {
            throw new NotFoundExceptionInterface($id);
        }
    }

    /**
     * {@inheritdoc}
     * @param string $name
     * @return bool
     */
    public function has($name): bool
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException(sprintf('The name parameter must be of type string, %s given',
                is_object($name) ? get_class($name) : gettype($name)));
        }

        return isset($this->instances[$name]) || isset($this->dependencies[$name]);
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return mixed|object|void
     */
    public function resolve(string $name, array $parameters = [])
    {

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $object = $this->build($name);
        $this->instances[$name] = $object;

        return $object;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getDependence(string $name): string
    {
        if (!isset($this->dependencies[$name])) {
            return $name;
        }
        return $this->dependencies[$name];
    }

    public function build($concrete)
    {
        $concrete = $this->getDependence($concrete);

        $reflector = new ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            return $this->notInstantiable($concrete);
        }

        $constructorMethod = $reflector->getConstructor();
        if ($constructorMethod !== null) {
            $constructorMethod->getParameters();
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


}
