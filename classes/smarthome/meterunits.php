<?php

namespace SmartHome;

use DB,PDO;

class MeterUnits {
    
    public static function getUnitId(string $description) {
        list($name,$unit)=explode(', ',$description);
        $s=DB::prepare('SELECT id FROM meter_units WHERE name=? AND unit=?');
        $s->execute([$name,$unit]);
        $id=$s->fetch(PDO::FETCH_COLUMN);
        if($id) {
            return $id;
        }
        $id=DB::insert('meter_units',['name'=>$name,'unit'=>$unit]);
        return $id;
    }
    
    public static function getUnitsList() {
        $s=DB::query('SELECT id,name FROM meter_units');
        return $s->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    public static function getUnitById($id) {
        $s=DB::prepare('SELECT id,name,unit FROM meter_units WHERE id=?');
        $s->execute([$id]);
        return $s->fetch(PDO::FETCH_OBJ);
    }
}