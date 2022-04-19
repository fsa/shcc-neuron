<?php

namespace SmartHome;

use App;

class DeviceStorage
{

    const REDIS_KEY_PREFIX = 'shcc:devices';

    public function init($devices)
    {
        foreach ($devices as $hwid => $entity) {
            App::redis()->setNx(self::REDIS_KEY_PREFIX . ':' . $hwid, serialize($entity));
        }
    }

    public function set(string $hwid, \SmartHome\DeviceInterface $object)
    {
        App::redis()->set(self::REDIS_KEY_PREFIX . ':' . $hwid, serialize($object));
    }

    public function get(string $hwid): ?\SmartHome\DeviceInterface
    {
        $device = unserialize(App::redis()->get(self::REDIS_KEY_PREFIX . ':' . $hwid));
        return $device ? $device : null;
    }

    public function exists(string $hwid): bool
    {
        return App::redis()->exists(self::REDIS_KEY_PREFIX . ':' . $hwid);
    }

    public static function getDevicesHwids()
    {
        $redis = App::redis();
        $redis->setOption(\Redis::OPT_SCAN, \Redis::SCAN_RETRY);
        $it = NULL;
        $result = [];
        while ($arr_keys = $redis->scan($it, self::REDIS_KEY_PREFIX . '*')) {
            foreach ($arr_keys as $str_key) {
                $result[] = $str_key;
            }
        }
        return $result;
    }
}
