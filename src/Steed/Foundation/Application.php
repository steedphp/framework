<?php

namespace Steed\Container;

use Steed\Contracts\Foundation\Application as ApplicationContract;

class Application implements ApplicationContract
{

    /**
     * The Steed framework version.
     *
     * @var string
     */
    const VERSION = '0.0.1';


    public function __construct()
    {
        $this->registerSingleton();
        $this->initialize();
    }


    protected function initialize()
    {
        $swooleServer = Container::getInstance()->get(\Steed\Contracts\Swoole\SwooleManager::class);


    }

    protected function registerSingleton()
    {
        foreach (
            [
                \Steed\Contracts\Swoole\SwooleManager::class => \Steed\Swoole\SwooleManager::class,
            ] as $abstract => $concrete) {
            Container::getInstance()->singleton($abstract, $concrete);
        }
    }

    protected function registerDefaultSwooleEvent()
    {

    }


}