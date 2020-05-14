<?php

namespace Steed\Framework\Config;

use Steed\Framework\Contracts\Config\Config as ConfigContracts;

/**
 * Class Repository
 * @package Steed\Framework\Repository
 */
class Repository implements ConfigContracts
{
    /**
     * @var array
     */
    private $configs;

    /**
     * Repository constructor.
     * @param array $configs
     */
    public function __construct(array $configs = [])
    {
        $this->configs = $configs;
    }

    /**
     * @param string $key
     * @param null $default
     * @return array|mixed|null
     */
    public function get(string $key, $default = null)
    {
        if ('.' == substr($key, -1)) {
            $key = substr($key, 0, -1);
            return isset($this->configs[$key]) ? $this->configs[$key] : [];
        }

        $name = explode('.', $key);
        $name[0] = strtolower($name[0]);
        $config = $this->configs;

        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return !is_null($this->get($key));
    }

    /**
     * @param string $key
     * @param null $value
     * @return bool
     */
    public function set(string $key, $value = null): bool
    {
        $keys = [$key => $value];

        foreach ($keys as $key => $value) {
            $this->configSet($key, $value);
        }
        return true;
    }

    /**
     * @param string $key
     * @param $value
     */
    protected function configSet(string $key, $value): void
    {
        $key = explode('.', $key, 3);

        if (count($key) == 2) {
            $this->configs[strtolower($key[0])][$key[1]] = $value;
        } elseif (count($key) == 3) {
            $this->configs[strtolower($key[0])][$key[1]][$key[2]] = $value;
        } else {
            $this->configs[strtolower($key[0])] = $value;
        }
    }

}
