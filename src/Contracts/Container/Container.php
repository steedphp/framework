<?php

namespace Steed\Contracts\Container;

use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{

    /**
     * Get container instance
     *
     * @return mixed
     */
    public static function getInstance(): Container;

    /**
     * Get all $id
     * @return array
     */
    public function getNames(): array;

    /**
     * 创建实例
     *
     * @param $abstract
     * @param $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters);

    /**
     * 构建实例
     *
     * @param $concrete
     * @return mixed
     */
    public function build($concrete);


}