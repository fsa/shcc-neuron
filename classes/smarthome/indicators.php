<?php

namespace SmartHome;

use DB,
    PDO;

class Indicators {
    
    private $indicator;

    public function create() {
        $this->indicator=new Entity\Indicator;
    }
    
    public function setDeviceId($id) {
        $this->indicator->device_id=$id;
    }
    
    public function setProperty($property) {
        $this->indicator->property=$property;
    }
    
    public function insert() {
        $params=get_object_vars($this->indicator);
        unset($params['id']);
        $id=DB::insert('indicators',$params);
        $this->indicator->id=$id;
        return $id;
    }

    public static function getIndicatorsByDeviceId($id) {
        $s=DB::prepare('SELECT property,id FROM indicators WHERE device_id=?');
        $s->execute([$id]);
        return $s->fetchAll(PDO::FETCH_KEY_PAIR);
    }

}
