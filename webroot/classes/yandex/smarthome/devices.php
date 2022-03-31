<?php

namespace Yandex\SmartHome;

use FSA\Neuron\DB;

class Devices {

    public static function get(): \PDOStatement {
        $s=DB::query('SELECT uuid, name, description, type, capabilities FROM yandex_devices');
        return $s;
    }

    public static function getByUid($name) {
        $s=DB::prepare('SELECT uuid, name, description, type, capabilities FROM yandex_devices WHERE uuid=?');
        $s->execute([$name]);
        return $s->fetchObject();
    }

}
