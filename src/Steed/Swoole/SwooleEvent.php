<?php

namespace Steed\Swoole;

use Swoole\Server;

class SwooleEvent
{

    const onStart = 'start';
    const onShutdown = 'shutdown';
    const onWorkerStart = 'workerStart';
    const onWorkerStop = 'workerStop';
    const onWorkerExit = 'workerExit';
    const onConnect = 'connect';
    const onReceive = 'receive';
    const onPacket = 'packet';
    const onClose = 'close';
    const onTask = 'task';
    const onFinish = 'finish';
    const onPipeMessage = 'pipeMessage';
    const onWorkerError = 'workerError';
    const onManagerStart = 'managerStart';
    const onManagerStop = 'managerStop';

    public $event = [
        self::onStart,
        self::onShutdown,
        self::onWorkerStart,
        self::onWorkerStop,
        self::onWorkerExit,
        self::onConnect,
        self::onReceive,
        self::onPacket,
        self::onClose,
        self::onTask,
        self::onFinish,
        self::onPipeMessage,
        self::onWorkerError,
        self::onManagerStart,
        self::onManagerStop
    ];

    public function start(Server $server)
    {
        echo $server->master_pid;
    }

    public function shutdown(Server $server)
    {

    }

    public function workerStart(Server $server, int $workerId)
    {

    }

    public function workerStop(Server $server, int $workerId)
    {

    }

    public function workerExit(Server $server, int $workerId)
    {

    }

    public function connect(Server $server, int $fd, int $reactorId)
    {

    }

    public function receive(Server $server, int $fd, int $reactorId, string $data)
    {

    }

    public function packet(Server $server, string $data, array $clientInfo)
    {

    }

    public function close(Server $server, int $fd, int $reactorId)
    {

    }

    public function task(Server $server, int $taskId, int $workerId, $data)
    {

    }

    public function finish(Server $server, int $taskId, string $data)
    {

    }

    public function pipeMessage(Server $server, int $workerId, $data)
    {

    }

    public function workerError(Server $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {

    }

    public function managerStart(Server $server)
    {

    }

    public function managerStop(Server $server)
    {

    }

}
