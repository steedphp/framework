<?php

namespace Steed\Swoole;

use Steed\Contracts\Swoole\SwooleManager as SwooleManagerContracts;
use Swoole\Server;

class SwooleManager implements SwooleManagerContracts
{

    private $server;

    /**
     *
     * @param $port
     * @param $type
     * @param string $address
     * @param array $setting
     * @param mixed ...$args
     * @return bool
     */
    public function createSwooleServer($port, $type, $address = '0.0.0.0', array $setting = [], ...$args): bool
    {

        switch ($type) {

            case 'WEB_SERVER':
            {
                $this->server = new \Swoole\Http\Server($address, $port, ...$args);
                break;
            }
            case 'WEB_SOCKET_SERVER':
            {
                $this->server = new \Swoole\Websocket\Server($address, $port, ...$args);
                break;
            }
            default:
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @return Server
     */
    public function getSwooleServer(): Server
    {
        return $this->server;
    }

    /**
     * swoole start
     */
    public function start(): void
    {
        $this->getSwooleServer()->start();
    }

}
