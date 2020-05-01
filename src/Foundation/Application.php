<?php

namespace Steed\Foundation;

use Steed\Container\Container;
use Steed\Contracts\Foundation\Application as ApplicationContract;
use Steed\Http\Dispatcher;
use Steed\Http\Request;
use Steed\Http\Response;
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
        $config = Container::getInstance()->get(\Steed\Contracts\Config\Config::class);

        $swooleServer = Container::getInstance()->get(\Steed\Contracts\Swoole\SwooleManager::class);
        $swooleServer->createSwooleServer(
            $config->get('app.server_port'),
            $config->get('app.server_type'),
            $config->get('app.server_address'),
            []
        );
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

        $config = Container::getInstance()->get(\Steed\Contracts\Config\Config::class);
        if ($config->get('app.server_type') == 'WEB_SERVER') {

            $dispatcher = new Dispatcher();

            $server->on('request', function ($request, $response) use ($dispatcher) {
                $request = new Request($request);
                $response = new Response($response);

                $dispatcher->dispatch($request, $response);
                $config = Container::getInstance()->get(\Steed\Contracts\Config\Config::class);
                $data = json_encode($config->get('app.index.config'));

                $response->__response();
            });

        }

        //TODO websocket event

    }


}