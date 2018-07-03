<?php

namespace SmartHome;

use DB,
    PDO;

class Devices {

    private $device;

    public function __construct() {
        
    }
    
    public static function get($uname) {
        $s=DB::prepare('SELECT m.name AS module,d.* FROM devices d LEFT JOIN modules m ON d.module_id=m.id WHERE d.unique_name=?');
        $s->execute([$uname]);
        $dev=$s->fetch(PDO::FETCH_OBJ);
        if(!$dev) {
            return null;
        }
        $mem=new DeviceMemoryStorage;
        $device=$mem->getDevice($dev->module,$dev->uid);
        if(is_null($device)) {
            $device=new $dev->classname;
            $device->init($dev->uid,json_decode($dev->init_data));
        }
        return $device;
    }

    public function create() {
        $this->device=new Entity\Device;
    }
    
    public function fetchDeviceById($id) {
        $s=DB::prepare('SELECT * FROM devices WHERE id=?');
        $s->execute([$id]);
        $s->setFetchMode(PDO::FETCH_CLASS,Entity\Device::class);
        $this->device=$s->fetch();
    }

    public function fetchDeviceByUid($module,$id) {
        $s=DB::prepare('SELECT d.* FROM devices d LEFT JOIN modules m ON d.module_id=m.id WHERE m.name=? AND d.uid=?');
        $s->execute([$module,$id]);
        $s->setFetchMode(PDO::FETCH_CLASS,Entity\Device::class);
        $this->device=$s->fetch();
    }
    
    public function exists($except=false) {
        if($except) {
            if($this->device instanceof Entity\Device) {
                return;
            }
            throw new Exception('Отсутствует устройство');
        }
        return $this->device instanceof Entity\Device;
    }

    public function getDevice() {
        return $this->device;
    }
    
    public function setDeviceProperties(array $data) {
        $this->exists(true);
        foreach ($data as $param=>$value) {
            $this->device->$param=$value;
        }
    }
    
    public function update() {
        $this->exists(true);
        return DB::update('devices',get_object_vars($this->device));
    }

    public function insert() {
        $this->exists(true);
        $params=get_object_vars($this->device);
        unset($params['id']);
        $id=DB::insert('devices',$params);
        $this->device->id=$id;
        return $id;
    }
    
    public static function getDeviceById($id) {
        $s=DB::prepare('SELECT * FROM devices WHERE id=?');
        $s->execute([$id]);
        $s->setFetchMode(PDO::FETCH_CLASS,Entity\Device::class);
        return $s->fetch();
    }
}
