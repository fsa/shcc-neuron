<?php

namespace SmartHome;

use DBRedis;

class DeviceStorage {

    const REDIS_KEY_PREFIX='shcc:devices';

    private $transaction;

    public function __construct() {

    }

    public function init($devices) {
        foreach ($devices as $hwid=> $entity) {
            DBRedis::hSetNx(self::REDIS_KEY_PREFIX,$hwid, serialize($entity));
        }
    }

    public function set(string $hwid, \SmartHome\DeviceInterface $object) {
        DBRedis::hSet(self::REDIS_KEY_PREFIX, $hwid, serialize($object));
    }

    public function get(string $hwid): ?\SmartHome\DeviceInterface {
        $device=unserialize(DBRedis::hGet(self::REDIS_KEY_PREFIX,$hwid));
        return $device?$device:null;
    }

    public function exists(string $hwid): bool {
        return DBRedis::hExists(self::REDIS_KEY_PREFIX,$hwid);
    }

    public static function getDevicesHwids() {
        return DBRedis::hKeys(self::REDIS_KEY_PREFIX);
    }

}
