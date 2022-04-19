<?php

namespace SmartHome;

use App;

class Vars {

    const REDIS_KEY='shcc:vars';

    public static function get($name) {
        return App::getVar($name);
    }

    public static function set($name, $value) {
        App::setVar($name, $value);
    }

    public static function getJson($name, $array=true) {
        return App::getVarJson($name, $array);
    }

    public static function setJson($name, $object) {
        App::setVarJson($name, $object);
    }

    public static function drop($name) {
        return App::dropVar($name);
    }

}
