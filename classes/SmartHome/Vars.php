<?php

namespace SmartHome;

use App;

class Vars {

    const REDIS_KEY='shcc:vars';

    public static function get($name) {
        return App::redis()->hget(self::REDIS_KEY, $name);
    }

    public static function set($name, $value) {
        App::redis()->hset(self::REDIS_KEY, $name, $value);
    }

    public static function getJson($name, $array=true) {
        $val=self::get($name);
        return json_decode($val, $array);
    }

    public static function setJson($name, $object) {
        self::set($name, json_encode($object));
    }

    public static function drop($name) {
        return App::redis()->del(self::REDIS_KEY, $name);
    }

}
