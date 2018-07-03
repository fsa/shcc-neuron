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

    public static function set($name,$value) {
        $s=DB::prepare('INSERT INTO variables (name,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?');
        $s->execute([$name,$value,$value]);
    }

    public static function getObject($name) {
        $val=self::get($name);
        return unserialize($val);
    }

    public static function setObject($name,$object) {
        self::set($name,serialize($object));
    }

    public static function getJson($name,$array=true) {
        $val=self::get($name);
        return json_decode($val,$array);
    }

    public static function setJson($name,$object) {
        self::set($name,json_encode($object));
    }

}
