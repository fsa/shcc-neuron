<?php

namespace SmartHome;

use DB,
    PDO;

class Meters {
    
    private $meter;

    public function create() {
        $this->meter=new Entity\Meter;
    }
    
    public function setDeviceId($id) {
        $this->meter->device_id=$id;
    }
    
    public function setProperty($property,$description) {
        $this->meter->property=$property;
        $this->meter->meter_unit_id=MeterUnits::getUnitId($description);
    }
    
    public function insert() {
        $params=get_object_vars($this->meter);
        unset($params['id']);
        $id=DB::insert('meters',$params);
        $this->meter->id=$id;
        return $id;
    }
    
    public static function getMetersByUnitId($id) {
        $s=DB::prepare('SELECT m.id, d.place_id, p.name FROM meters m LEFT JOIN devices d ON m.device_id=d.id LEFT JOIN places p ON d.place_id=p.id WHERE meter_unit_id=?');
        $s->execute([$id]);
        return $s->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getMetersByDeviceId($id) {
        $s=DB::prepare('SELECT property,id FROM meters WHERE device_id=?');
        $s->execute([$id]);
        return $s->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    public static function getMetersPlaces(int $unit): array {
        $s=DB::prepare('SELECT p.id, p.name FROM (SELECT place_id FROM meter_history WHERE meter_unit_id=? GROUP BY place_id) mp LEFT JOIN places p ON p.id=mp.place_id');
        $s->execute([$unit]);
        return $s->fetchAll(PDO::FETCH_OBJ);
    }

}
