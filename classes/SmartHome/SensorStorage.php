<?php

namespace SmartHome;

use App;

class SensorStorage
{

    const STORAGE_NAME = 'shcc:sensors';

    public static function set(string $uid, $value, $ts = null)
    {
        App::redis()->set(self::STORAGE_NAME . ':' . $uid, json_encode(["value" => $value, "ts" => is_null($ts) ? time() : $ts]));
    }

    public static function get(string $uid)
    {
        $sensor = App::redis()->get(self::STORAGE_NAME . ':' . $uid);
        return $sensor ? json_decode($sensor) : null;
    }

    public static function rename(string $old, string $new)
    {
        if (!App::redis()->exists(self::STORAGE_NAME.':'.$old)) {
            return;
        }
        App::redis()->rename(self::STORAGE_NAME . ':' . $old, self::STORAGE_NAME . ':' . $new);
    }
}
