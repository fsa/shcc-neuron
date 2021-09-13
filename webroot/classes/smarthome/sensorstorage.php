<?php

namespace SmartHome;

use DBRedis;

class SensorStorage {

    const STORAGE_NAME='shcc:sensors';

    public static function set(string $uid, $value, $ts=null) {
        DBRedis::hSet(self::STORAGE_NAME, $uid, json_encode(["value"=>$value, "ts"=>is_null($ts)?time():$ts]));
    }

    public static function get(string $uid) {
        $sensor=DBRedis::hGet(self::STORAGE_NAME, $uid);
        return $sensor?json_decode($sensor):null;
    }

    public static function storeEvents($device_uid, $events, $ts=null) {
        foreach ($events as $property=> $value) {
            self::storeEvent($device_uid, $property, $value, $ts);
        }
    }

}
