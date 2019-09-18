<?php

namespace Yandex\SmartHome;

use DB,
    PDO,
    PDOStatement;

class Devices {

    public static function get($user_id): PDOStatement {
        $s=DB::prepare('SELECT yd.uid, yd.user_id, yd.name, yd.description, p.name AS room, yd.type, yd.capabilities, d.unique_name FROM yandex_devices yd LEFT JOIN places p ON yd.place_id=p.id LEFT JOIN devices d ON yd.device_id=d.id WHERE user_id=? OR user_id IS NULL');
        $s->execute([$user_id]);
        $s->setFetchMode(PDO::FETCH_OBJ);
        return $s;
    }

    public static function getByUid($name, $user_id) {
        $s=DB::prepare('SELECT yd.uid, yd.user_id, yd.name, yd.description, p.name AS room, yd.type, yd.capabilities, d.unique_name FROM yandex_devices yd LEFT JOIN places p ON yd.place_id=p.id LEFT JOIN devices d ON yd.device_id=d.id WHERE yd.uid=? AND (yd.user_id=? OR yd.user_id IS NULL)');
        $s->execute([$name, $user_id]);
        return $s->fetch(PDO::FETCH_OBJ);
    }

}
