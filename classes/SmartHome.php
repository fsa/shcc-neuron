<?php

use FSA\SmartHome\DeviceFactory;
use FSA\SmartHome\DeviceStorage;

class SmartHome
{
    private static $device_factory;
    private static $device_storage;

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
            self::$device_storage = new DeviceStorage(App::redis(), App::REDIS_PREFIX . ':devices:');
        }
        return self::$device_storage;
    }
}
