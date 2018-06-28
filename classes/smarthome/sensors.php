<?php

namespace SmartHome;

use DB,PDO;

class Sensors {

    public static function getDeviceMeters($module,$uid) {
        $stmt=DB::prepare('SELECT m.id, m.property, d.place_id, m.measure_id FROM meters m LEFT JOIN devices d ON m.device_id=d.id LEFT JOIN modules md ON d.module_id=md.id WHERE md.name=? AND d.uid=?');
        $stmt->execute([$module,$uid]);
        $sensors=$stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $sensors;
    }

    public static function getDeviceIndicators($module,$uid) {
        $stmt=DB::prepare('SELECT i.id, i.property, d.place_id FROM indicators i LEFT JOIN devices d ON i.device_id=d.id LEFT JOIN modules m ON d.module_id=m.id WHERE m.name=? AND d.uid=?');
        $stmt->execute([$module,$uid]);
        $sensors=$stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $sensors;
    }

}
