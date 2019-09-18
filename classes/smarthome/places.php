<?php

namespace SmartHome;

use DB,
    PDO;

class Places {
    private $place;
    
    public function __construct() {
        
    }
    
    public function fetch($id) {
        $s=DB::prepare('SELECT * FROM places WHERE id=?');
        $s->execute([$id]);
        $s->setFetchMode(PDO::FETCH_CLASS,Entity\Place::class);
        $this->place=$s->fetch();
    }
    
    public function getPlace(): Entity\Place {
        return $this->place;
    }
    
    public function createPlace($id,$pid,$name) {
        if($name=='') {
            throw new \AppException('Имя места должно быть заполнено');
        }
        if(!$id) {
            $id=null;
        }
        if(!$pid) {
            $pid=null;
        }
        $place=new Entity\Place;
        $place->id=$id;
        $place->pid=$pid;
        $place->name=$name;
        $this->place=$place;
    }
    
    public function upsert() {
        if(is_null($this->place->id)) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    public function update() {
        return DB::update('places',get_object_vars($this->place));
    }
    
    public function insert() {
        $params=get_object_vars($this->place);
        unset($params['id']);
        $id=DB::insert('places',$params);
        $this->place->id=$id;
        return $id;
    }
    
    public static function create($name, $pid) {
        $s=DB::prepare('INSERT INTO places (name, pid) VALUES (?,?) RETURNING id, name AS text');
        $s->execute([$name, $pid]);
        return $s->fetch(PDO::FETCH_OBJ);
    }

    public static function move($id, $parent) {
        $s=DB::prepare('UPDATE places SET pid=? WHERE id=?');
        $s->execute([$parent, $id]);
        return $s->rowCount();        
    }

    public static function delete($id) {
        $s=DB::prepare('DELETE FROM places WHERE id=?');
        $s->execute([$id]);
        return $s->rowCount();
    }
    
    public static function rename($id, $name) {
        $s=DB::prepare('UPDATE places SET name=? WHERE id=?');
        $s->execute([$name, $id]);
        return $s->rowCount();        
    }

    public static function getRootPlaces($fetch_stype=PDO::FETCH_KEY_PAIR) {
        $s=DB::query('SELECT id, name AS text FROM places WHERE pid IS NULL ORDER BY id');
        return $s->fetchAll($fetch_stype);
    }

    public static function getPlaceChild($id, $fetch_stype=PDO::FETCH_KEY_PAIR) {
        $s=DB::prepare('SELECT id, name AS text FROM places WHERE pid=? ORDER BY id');
        $s->execute([$id]);
        return $s->fetchAll($fetch_stype);
    }
    
    public static function getPlaceListStmt() {
        $s=DB::query("SELECT NULL, '-----' UNION SELECT id, name FROM places");
        return $s;
    }

}
