<?php

use FSA\SmartHome\DeviceFactory;
use FSA\SmartHome\DeviceStorage;
use FSA\SmartHome\DeviceDatabase;

class SmartHome
{
    private static $device_factory;
    private static $device_storage;
    private static $device_database;

    public static function device(): DeviceFactory
    {
        if (is_null(self::$device_factory)) {
            self::$device_factory = new DeviceFactory(Plugins::get());
        }
        return self::$device_factory;
    }

    public static function deviceStorage(): DeviceStorage
    {
        if (is_null(self::$device_storage)) {
            self::$device_storage = new DeviceStorage(App::redis(), App::REDIS_PREFIX . ':devices');
        }
        return self::$device_storage;
    }

    public static function deviceDatabase(): DeviceDatabase
    {
        if (is_null(self::$device_database)) {
            self::$device_database = new DeviceDatabase(App::sql());
        }
        return self::$device_database;
    }

    public static function getDevice($uid)
    {
        $device = self::deviceDatabase()->get($uid);
        if ($device) {
            return self::deviceStorage()->get($device->plugin, $device->hwid);
        }
        return null;
    }

    public static function setDevice($uid, $object)
    {
        $device = self::deviceDatabase()->get($uid);
        if ($device) {
            return self::deviceStorage()->set($device->plugin, $device->hwid, $object);
        }
        return null;
    }
}
