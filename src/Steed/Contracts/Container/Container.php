<?php

namespace Steed\Contracts\Container;

use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{

    /**
     * 单例注册到容器中
     *
     * @param string $abstract
     * @param \Closure|string|null $concrete
     * @return void
     */
    public function singleton(string $abstract, $concrete = null);

    /**
     * Get container instance
     *
     * @return mixed
     */
    public static function getInstance(): Container;

    /**
     * 注册实例
     *
     * @param $abstract
     * @param $instance
     * @return mixed
     */
    public function instance($abstract, $instance);

    /**
     * 清空容器中实例
     *
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

    /**
     * 创建实例
     *
     * @param $abstract
     * @param $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters);

    /**
     * 获取抽象具体
     *
     * @param $abstract
     * @return mixed
     */
    public function getConcrete($abstract);

    /**
     * 构建实例
     *
     * @param $concrete
     * @return mixed
     */
    public function build($concrete);

    /**
     * $abstract 与 $concrete绑定
     *
     * @param $abstract
     * @param null $concrete
     * @param bool $shared
     * @return mixed
     */
    public function bind(string $abstract, string $concrete = null, $shared = true);
}