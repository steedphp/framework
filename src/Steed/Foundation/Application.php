<?php

namespace Steed\Foundation;

use Steed\Container\Container;
use Steed\Contracts\Foundation\Application as ApplicationContract;
use Steed\Swoole\SwooleEvent;
use Swoole\Server;

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


    protected function initialize(): void
    {
        Container::getInstance()->get(\Steed\Contracts\Config\Config::class);

        $swooleServer = Container::getInstance()->get(\Steed\Contracts\Swoole\SwooleManager::class);
        $swooleServer->createSwooleServer(9001, 'WEB_SERVER', $address = '0.0.0.0', []);
        $this->registerDefaultSwooleEvent($swooleServer->getSwooleServer());
        $swooleServer->start();
    }

    protected function registerSingleton()
    {
        foreach (
            [
                \Steed\Contracts\Swoole\SwooleManager::class => \Steed\Swoole\SwooleManager::class,
                \Steed\Contracts\Config\Config::class => \Steed\Config\Config::class,
            ] as $abstract => $concrete) {
            Container::getInstance()->singleton($abstract, $concrete);
        }
    }

    protected function registerDefaultSwooleEvent(Server $server): void
    {
        $swooleEvent = new SwooleEvent();

        foreach ($swooleEvent->event as $event) {
            $server->on($event, [$swooleEvent, $event]);
        }
//TODO http server event
        $server->on('request', function ($request, $response) {

            $config = Container::getInstance()->get(\Steed\Contracts\Config\Config::class);
            $data = json_encode($config->all());
            $response->end($data);
        });
    }


}