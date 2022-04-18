<?php

namespace Yandex\SmartHome;

use App;

class Devices
{

    public static function get(): \PDOStatement
    {
        $s = App::sql()->query('SELECT uuid, name, description, type, capabilities FROM yandex_devices');
        return $s;
    }

    public static function getByUid($name)
    {
        $s = App::sql()->prepare('SELECT uuid, name, description, type, capabilities FROM yandex_devices WHERE uuid=?');
        $s->execute([$name]);
        return $s->fetchObject();
    }
}
