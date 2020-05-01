<?php

namespace Steed\Config;

use Steed\Contracts\Config\Config as ConfigContracts;

class Config implements ConfigContracts
{
    private $config = [];


    protected $path;

    protected $configExt;

    public function __construct(string $path = '', string $configExt = 'php')
    {
        //TODO $path 动态配置
        $this->path = CONFIG_PATH . DIRECTORY_SEPARATOR;
        $this->configExt = $configExt;

        $this->initialize();
    }

    protected function initialize()
    {
        //读取配置文件并加载
        $files = isset($this->path) ? scandir($this->path) : [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === $this->configExt) {
                $this->loadFile($this->path . $file, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }

    protected function loadFile($filename, $name): bool
    {
        $name = strtolower($name);
        $type = pathinfo($filename, PATHINFO_EXTENSION);

        if ('php' == $type) {
            return $this->set($name, include_once "Config.php");
        }

        return true;
    }

    public function all()
    {
        return $this->config;
    }


    public function get($key, $default = null)
    {
        if ('.' == substr($key, -1)) {
            $key = substr($key, 0, -1);
            return isset($this->config[$key]) ? $this->config[$key] : [];
        }

        $name = explode('.', $key);
        $name[0] = strtolower($name[0]);
        $config = $this->config;

        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }

    public function has($key): bool
    {
        return !is_null($this->get($key));
    }

    /**
     * @param $key
     * @param null $value
     * @return bool
     */
    public function set($key, $value = null): bool
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            $this->configSet($key, $value);
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
    protected function configSet($key, $value)
    {
        if (is_string($key)) {

            $key = explode('.', $key, 3);

            if (count($key) == 2) {
                $this->config[strtolower($key[0])][$key[1]] = $value;
            } elseif (count($key) == 3) {
                $this->config[strtolower($key[0])][$key[1]][$key[2]] = $value;
            } else {
                $this->config[strtolower($key[0])] = $value;
            }

            return $value;
        } elseif (is_array($key)) {
            // 批量设置
            if (!empty($value)) {
                if (isset($this->config[$value])) {
                    $result = array_merge($this->config[$value], $key);
                } else {
                    $result = $key;
                }

                $this->config[$value] = $result;
            } else {
                $result = $this->config = array_merge($this->config, $key);
            }
        } else {
            // 为空直接返回 已有配置
            $result = $this->config;
        }

        return $result;
    }

}
