<?php

namespace SmartHome;

class Meters {

    public static function getDeviceMeters($module,$uid) {
        $stmt=DB::prepare('SELECT m.id, m.property, d.place_id, m.measure_id FROM meters m LEFT JOIN devices d ON m.device_id=d.id LEFT JOIN modules md ON d.module_id=md.id WHERE md.name=? AND d.uid=?');
        $stmt->execute([$module,$uid]);
        $sensors=$stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $sensors;
    }

}
