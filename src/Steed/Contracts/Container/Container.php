<?php

namespace Steed\Contracts\Container;

use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{

    /**
     * Register a shared binding in the container.
     *
     * @param string $abstract
     * @param \Closure|string|null $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null);

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string $abstract
     * @param mixed $instance
     * @return mixed
     */
    public function instance($abstract, $instance);

    /**
     * Clear container instance
     * @return mixed
     */
    public function flush();

    /**
     * getStats
     * @return array
     */
    public function getStats(): array;

    /**
     * Get all $id
     * @return array
     */
    public function getNames(): array;
}