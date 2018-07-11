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
        #TODO по опианию найти единицу измерения или оздать
    }
    
    public function insert() {
        $params=get_object_vars($this->meter);
        unset($params['id']);
        $id=DB::insert('meters',$params);
        $this->meter->id=$id;
        return $id;
    }
    
    public static function getMetersByUnitId($id) {
        $s=DB::prepare('SELECT m.id, d.place_id, p.name FROM meters m LEFT JOIN devices d ON m.device_id=d.id LEFT JOIN places p ON d.place_id=p.id WHERE meter_unit_id');
        $s->execute([$id]);
        return $s->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getMetersByDeviceId($id) {
        $s=DB::prepare('SELECT property,id FROM meters WHERE device_id=?');
        $s->execute([$id]);
        return $s->fetchAll(PDO::FETCH_KEY_PAIR);
    }

}
