<?php

namespace SmartHome;

use FSA\Neuron\DBRedis;

class SensorStorage {

    const STORAGE_NAME='shcc:sensors';

    public static function set(string $uid, $value, $ts=null) {
        DBRedis::hSet(self::STORAGE_NAME, $uid, json_encode(["value"=>$value, "ts"=>is_null($ts)?time():$ts]));
    }

    public static function get(string $uid) {
        $sensor=DBRedis::hGet(self::STORAGE_NAME, $uid);
        return $sensor?json_decode($sensor):null;
    }

}