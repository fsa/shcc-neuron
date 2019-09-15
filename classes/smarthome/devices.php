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
        $mem=new Device\MemoryStorage;
        $device=$mem->getDevice($dev->module.'_'.$dev->uid);
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
    
    public function setDevice(Entity\Device $device) {
        $this->device=$device;
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
    
    public static function getDevicesStmt(): \PDOStatement {
        $s=DB::query("SELECT d.id, d.unique_name, d.uid, d.description, d.classname, p.name AS place, CASE disabled WHEN true THEN 'table-danger' END AS style FROM devices d LEFT JOIN places p ON d.place_id=p.id ORDER BY d.uid");
        $s->setFetchMode(PDO::FETCH_OBJ);
        return $s;
    }
    
    public static function getDeviceById($id): Entity\Device {
        $s=DB::prepare('SELECT * FROM devices WHERE id=?');
        $s->execute([$id]);
        $s->setFetchMode(PDO::FETCH_CLASS,Entity\Device::class);
        return $s->fetch();
    }
    
    public static function getUniqueNameByUid($module,$uid) {
        $s=DB::prepare('SELECT unique_name FROM devices d LEFT JOIN modules m ON d.module_id=m.id WHERE m.name=? AND d.uid=?');
        $s->execute([$module,$uid]);
        return $s->fetch(PDO::FETCH_COLUMN);
    }
    
    public static function refreshMemoryDevices(string $module_name): void {
        $stmt=DB::prepare('SELECT d.uid,d.classname,d.init_data FROM devices d LEFT JOIN modules m ON d.module_id=m.id WHERE m.name=? AND d.disabled=false');
        $stmt->execute([$module_name]);
        $mem=new Device\MemoryStorage();
        $mem->lockMemory();
        while ($device=$stmt->fetch(\PDO::FETCH_OBJ)) {
            if ($mem->existsDevice($device->uid)) {
                $device_obj=$mem->getDevice($device->uid);
            } else {
                $device_obj=new $device->classname;
            }
            $data=json_decode($device->init_data,true);
            if (!is_array($data)) {
                $data=[];
            }
            $device_obj->init($device->uid,$data);
            $mem->setDevice($device->uid, $device_obj);
        }
        $mem->releaseMemory();
    }

    public static function getMeters($uid) {
        $stmt=DB::prepare('SELECT m.id, m.property, d.place_id, m.meter_unit_id FROM meters m LEFT JOIN devices d ON m.device_id=d.id WHERE d.uid=?');
        $stmt->execute([$uid]);
        $sensors=$stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $sensors;
    }

    public static function getIndicators(string $uid) {
        $stmt=DB::prepare('SELECT i.id, i.property, d.place_id FROM indicators i LEFT JOIN devices d ON i.device_id=d.id WHERE d.uid=?');
        $stmt->execute([$uid]);
        $sensors=$stmt->fetchAll(PDO::FETCH_OBJ);
        $stmt->closeCursor();
        return $sensors;
    }


}
