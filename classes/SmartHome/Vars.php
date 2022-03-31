<?php

namespace SmartHome;

use FSA\Neuron\DBRedis;

class Vars {

    const REDIS_KEY='shcc:vars';

    public static function get($name) {
        return DBRedis::hget(self::REDIS_KEY, $name);
    }

    public static function set($name, $value) {
        DBRedis::hset(self::REDIS_KEY, $name, $value);
    }

    public static function getJson($name, $array=true) {
        $val=self::get($name);
        return json_decode($val, $array);
    }

    public static function setJson($name, $object) {
        self::set($name, json_encode($object));
    }

    public static function drop($name) {
        return DBRedis::del(self::REDIS_KEY, $name);
    }

}
