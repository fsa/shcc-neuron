<?php

use FSA\SmartHome\{DeviceFactory, DeviceStorage, DeviceDatabase, SensorDatabase, SensorStorage, TTS\Queue};

class SmartHome
{
    private static $device_factory;
    private static $device_storage;
    private static $device_database;
    private static $sensor_storage;
    private static $sensor_database;
    private static $tts;

    public static function deviceFactory(): DeviceFactory
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

    public static function sensorStorage(): SensorStorage
    {
        if (is_null(self::$sensor_storage)) {
            self::$sensor_storage = new SensorStorage(App::redis(), App::REDIS_PREFIX . ':sensors');
        }
        return self::$sensor_storage;
    }

    public static function sensorDatabase(): SensorDatabase
    {
        if (is_null(self::$sensor_database)) {
            self::$sensor_database = new SensorDatabase(App::sql());
        }
        return self::$sensor_database;
    }

    public static function tts(): Queue
    {
        if (is_null(self::$tts)) {
            self::$tts = new Queue(App::redis(), App::REDIS_PREFIX . ':TTS');
        }
        return self::$tts;
    }

    public static function storeEvents($uid, $events, $ts = null)
    {
        $sensor = self::sensorDatabase();
        foreach ($events as $property => $value) {
            $sensor->storeEvent($uid, $property, $value, $ts);
        }
    }

    public static function execEventsCustomScripts($uid, $events, $ts = null)
    {
        $custom_dir = __DIR__ . '/../custom/events/';
        if (!file_exists($custom_dir . $uid . '.php')) {
            return;
        }
        chdir($custom_dir);
        $eventsListener = require $uid . '.php';
        $eventsListener->uid = $uid;
        foreach ($events as $event => $value) {
            $method = 'on_event_' . str_replace('@', '_', $event);
            if (method_exists($eventsListener, $method)) {
                $eventsListener->$method($value, $ts);
            }
        }
    }

    public static function processEvents($plugin, $hwid, $events, $ts = null)
    {
        $uid = self::deviceDatabase()->searchUid($plugin, $hwid);
        if (!$uid) {
            return;
        }
        try {
            self::storeEvents($uid, $events, $ts);
        } catch (Exception $ex) {
            syslog(LOG_ERR, 'Ошибка при сохранении данных с датчиков:' . PHP_EOL . $ex);
        }
        try {
            self::execEventsCustomScripts($uid, $events, $ts);
        } catch (Exception $ex) {
            syslog(LOG_ERR, 'Ошибка при выполнении пользовательского скрипта events/' . $uid . '.php:' . PHP_EOL . $ex);
        }
    }

}
