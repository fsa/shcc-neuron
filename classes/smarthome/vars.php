<?php

namespace SmartHome;

use DB,
    PDO;

class Vars {

    public static function get($name) {
        $s=DB::prepare('SELECT value FROM variables WHERE name=?');
        $s->execute([$name]);
        return $s->fetch(PDO::FETCH_COLUMN);
    }

    public static function set($name, $value) {
        $s=DB::prepare('INSERT INTO variables (name,value) VALUES (?,?) ON CONFLICT (name) DO UPDATE SET value=?');
        $s->execute([$name, $value, $value]);
    }

    public static function getJson($name, $array=true) {
        $val=self::get($name);
        return json_decode($val, $array);
    }

    public static function setJson($name, $object) {
        self::set($name, json_encode($object));
    }

    public static function drop($name) {
        $s=DB::prepare('DELETE FROM variables WHERE name=?');
        $s->execute([$name]);
    }

}
