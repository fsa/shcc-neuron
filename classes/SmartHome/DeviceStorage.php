<?php

namespace SmartHome;

use App;

class DeviceStorage {

    const REDIS_KEY_PREFIX='shcc:devices';

    private $transaction;

    public function __construct() {

    }

    public function init($devices) {
        foreach ($devices as $hwid=> $entity) {
            App::redis()->hSetNx(self::REDIS_KEY_PREFIX,$hwid, serialize($entity));
        }
    }

    public function set(string $hwid, \SmartHome\DeviceInterface $object) {
        App::redis()->hSet(self::REDIS_KEY_PREFIX, $hwid, serialize($object));
    }

    public function get(string $hwid): ?\SmartHome\DeviceInterface {
        $device=unserialize(App::redis()->hGet(self::REDIS_KEY_PREFIX,$hwid));
        return $device?$device:null;
    }

    public function exists(string $hwid): bool {
        return App::redis()->hExists(self::REDIS_KEY_PREFIX,$hwid);
    }

    public static function getDevicesHwids() {
        return App::redis()->hKeys(self::REDIS_KEY_PREFIX);
    }

}
