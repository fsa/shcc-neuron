<?php

namespace FSA\SmartHome;

use Redis;

class DeviceStorage
{
    private $redis;
    private $key_prefix;

    public function __construct($redis, $key_prefix)
    {
        $this->redis = $redis;
        $this->key_prefix = $key_prefix;
    }

    public function set(string $plugin, string $hwid, DeviceInterface $object)
    {
        $this->redis->set($this->key_prefix . ':' . $plugin . ':' . $hwid, serialize($object));
    }

    public function setNx(string $plugin, string $hwid, DeviceInterface $object)
    {
        $this->redis->setNx($this->key_prefix . ':' . $plugin . ':' . $hwid, serialize($object));
    }

    public function get(string $plugin, string $hwid): ?DeviceInterface
    {
        $device = unserialize($this->redis->get($this->key_prefix . ':' . $plugin . ':' . $hwid));
        return $device ? $device : null;
    }

    public function exists(string $plugin, string $hwid): bool
    {
        return $this->redis->exists($this->key_prefix . ':' . $plugin . ':' . $hwid);
    }

    public function getDevicesHwids($plugin = null)
    {
        $search_key = $this->key_prefix . ':' . (empty($plugin) ? '*' : ($plugin . ':' . '*'));
        $this->redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        $it = NULL;
        $result = [];
        while ($arr_keys = $this->redis->scan($it, $search_key)) {
            foreach ($arr_keys as $str_key) {
                $result[] = $str_key;
            }
        }
        return $result;
    }
}
