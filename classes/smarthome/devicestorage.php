<?php

namespace SmartHome;

use DBRedis;

class DeviceStorage {

    const REDIS_KEY_PREFIX='shcc:devices:';

    private $transaction;

    public function __construct() {

    }

    public function init($devices) {
        foreach ($devices as $hwid=> $entity) {
            DBRedis::setnx(self::REDIS_KEY_PREFIX.$hwid, serialize($entity));
        }
    }

    public function watch($hwid): void {
        DBRedis::watch(self::REDIS_KEY_PREFIX.$hwid);
        $this->transaction=$hwid;
    }

    public function unwatch(): void {
        DBRedis::unwatch(self::REDIS_KEY_PREFIX.$this->transaction);
        $this->transaction=null;
    }

    public function set(string $hwid, \SmartHome\DeviceInterface $object): bool {
        if ($this->transaction==$hwid) {
            return DBRedis::multi()->set(self::REDIS_KEY_PREFIX.$hwid, serialize($object))->exec();
        }
        DBRedis::set(self::REDIS_KEY_PREFIX.$hwid, serialize($object));
        return true;
    }

    public function get(string $hwid): ?\SmartHome\DeviceInterface {
        return unserialize(DBRedis::get(self::REDIS_KEY_PREFIX.$hwid));
    }

    public function exists(string $hwid): bool {
        return DBRedis::get(self::REDIS_KEY_PREFIX.$hwid);
    }

    public static function getDevicesHwids() {
        $iterator=null;
        return DBRedis::scan($iterator, self::REDIS_KEY_PREFIX.'*');
    }

}
