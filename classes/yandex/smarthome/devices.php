<?php

namespace Yandex\SmartHome;

use DB,
    PDO,
    PDOStatement;

class Devices {

    public static function get($user_id): PDOStatement {
        $s=DB::prepare('SELECT yd.uid, yd.name, yd.description, p.name AS room, yd.type, yd.capabilities FROM yandex_devices yd LEFT JOIN places p ON yd.place_id=p.id LEFT JOIN devices d ON yd.device_id=d.id WHERE user_id=?');
        $s->execute([$user_id]);
        $s->setFetchMode(PDO::FETCH_OBJ);
        return $s;
    }

}
