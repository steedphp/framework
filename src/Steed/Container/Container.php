<?php

namespace Steed\Container;

use ReflectionClass;
use Steed\Contracts\Container\Container as ContainerContracts;
use Steed\Exception\BindingResolutionException;
use Steed\Exception\EntryNotFoundException;

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

    /**
     * $bindings
     * @var array
     */
    protected $bindings = [];

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

    public function instance($abstract, $instance)
    {

    }

    /**
     * 绑定到容器中
     *
     * @param string $abstract
     * @param string|null $concrete
     * @param bool $shared 是否单例
     */
    public function bind(string $abstract, string $concrete = null, $shared = true)
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
     *
     * @param string $abstract
     * @param null $concrete
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
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
    public function has($abstract)
    {
        return isset($this->bindings[$abstract]) ||
            isset($this->instances[$abstract]);
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
        $concrete = $this->getConcrete($abstract);

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $object = $this->build($concrete);
        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Determine if a given type is shared.
     *
     * @param $abstract
     * @return bool
     */
    public function isShared(string $abstract): bool
    {
        return isset($this->instances[$abstract]) ||
            (isset($this->bindings[$abstract]['shared']) &&
                $this->bindings[$abstract]['shared'] === true);
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
     * Flush the container of all bindings and resolved instances.
     */
    public function flush(): void
    {
        $this->instances = [];
        $this->bindings = [];
        return;
    }

    /**
     * @return array
     */
    public function getStats(): array
    {
        return $this->bindings;
    }

    /**
     * @return array
     */
    public function getNames(): array
    {
        return array_keys($this->instances);
    }

}
