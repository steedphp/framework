<?php

namespace Steed\Http;


use Steed\Http\Message\Status;
use Swoole\Coroutine as Co;

class Dispatcher
{
    private $controllerNameSpaceBase;
    private $maxPoolNum;
    private $controllerPoolCreateNum = [];
    private $httpExceptionHandler = null;
    private $controllerPoolWaitTime = 5.0;

    function __construct(int $maxDepth = 5, int $maxPoolNum = 200)
    {
        $this->controllerNameSpaceBase = '\\App\\Http\\Controller';
        $this->maxPoolNum = $maxPoolNum;
    }

    public function dispatch(Request $request, Response $response): void
    {
        $path = UrlParser::pathInfo($request->getUri()->getPath());

        $request->getUri()->withPath($path);


        $this->controllerHandler($request, $response, $path);

    }

    private function controllerHandler(Request $request, Response $response, string $path)
    {
        $pathInfo = ltrim($path, "/");
        $list = explode("/", $pathInfo);
        $actionName = null;
        $finalClass = null;
        $currentDepth = count($list);

        while ($currentDepth >= 0) {
            $className = '';
            for ($i = 0; $i < $currentDepth; $i++) {
                $className = $className . "\\" . ucfirst($list[$i] ?: 'Index');
            }
            if (class_exists($this->controllerNameSpaceBase . $className)) {
                //尝试获取该class后的actionName
                $actionName = empty($list[$i]) ? 'index' : $list[$i];
                $finalClass = $this->controllerNameSpaceBase . $className;
                break;
            } else {
                //尝试搜搜index控制器
                $temp = $className . "\\Index";
                if (class_exists($this->controllerNameSpaceBase . $temp)) {
                    $finalClass = $this->controllerNameSpaceBase . $temp;
                    //尝试获取该class后的actionName
                    $actionName = empty($list[$i]) ? 'index' : $list[$i];
                    break;
                }
            }
            $currentDepth--;
        }
        if (!empty($finalClass)) {
            try {
                $controller = $this->getController($finalClass);
            } catch (\Throwable $throwable) {
                $this->hookThrowable($throwable, $request, $response);
                return;
            }
            if (is_object($controller)) {
                try {
                    $path = $controller->$actionName();
                    $response->write(json_encode($path));
                } catch (\Throwable $throwable) {
                    $this->hookThrowable($throwable, $request, $response);
                } finally {
                    $this->recycleController($finalClass, $controller);
                }
            }
        } else {
            $content = '<h1>hello word</h1>';
            $response->write($content);
        }
    }

    protected function getController(string $class)
    {
        $classKey = $this->generateClassKey($class);
        if (!isset($this->$classKey)) {
            $this->$classKey = new Co\Channel($this->maxPoolNum + 1);
            $this->controllerPoolCreateNum[$classKey] = 0;
        }
        $channel = $this->$classKey;
        //懒惰创建模式
        /** @var Co\Channel $channel */
        if ($channel->isEmpty()) {
            $createNum = $this->controllerPoolCreateNum[$classKey];
            if ($createNum < $this->maxPoolNum) {
                $this->controllerPoolCreateNum[$classKey] = $createNum + 1;
                try {
                    //防止用户在控制器结构函数做了什么东西导致异常
                    return new $class();
                } catch (\Throwable $exception) {
                    $this->controllerPoolCreateNum[$classKey] = $createNum;
                    //直接抛给上层
                    throw $exception;
                }
            }
            return $channel->pop($this->controllerPoolWaitTime);
        }
        return $channel->pop($this->controllerPoolWaitTime);
    }

    protected function recycleController(string $class, $obj)
    {
        $classKey = $this->generateClassKey($class);
        /** @var Co\Channel $channel */
        $channel = $this->$classKey;
        $channel->push($obj);
    }

    protected function hookThrowable(\Throwable $throwable, Request $request, Response $response)
    {
        if (is_callable($this->httpExceptionHandler)) {
            call_user_func($this->httpExceptionHandler, $throwable, $request, $response);
        } else {
            $response->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
            $response->write(nl2br($throwable->getMessage() . "\n" . $throwable->getTraceAsString()));
        }
    }

    protected function generateClassKey(string $class): string
    {
        return substr(md5($class), 8, 16);
    }
}