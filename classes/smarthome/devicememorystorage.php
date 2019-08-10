<?php

namespace SmartHome;

class DeviceMemoryStorage {

    const PROJ='a';
    const CHMOD=0600;

    private $shm;

    public function __construct() {
        $memsize=\Settings::get('device_memory_storage');
        if (is_null($memsize)) {
            $memsize=1048576; # 1MB
        }
        $file=ftok(__FILE__,self::PROJ);
        $this->shm=shm_attach($file,$memsize,self::CHMOD);
    }

    public function getModuleDevices(string $module_name) {
        $key=$this->getKeyByModuleName($module_name);
        $array=@shm_get_var($this->shm,$key);
        if ($array===false or !is_array($array)) {
            $array=[];
            $stmt=\DB::prepare('SELECT d.uid,d.classname,d.init_data FROM devices d LEFT JOIN modules m ON d.module_id=m.id WHERE m.name=? AND d.disabled=false');
            $stmt->execute([$module_name]);
            while ($device=$stmt->fetch(\PDO::FETCH_OBJ)) {
                $device_obj=new $device->classname;
                $data=json_decode($device->init_data,true);
                if (!is_array($data)) {
                    $data=[];
                }
                $device_obj->init($device->uid,$data);
                $array[$device->uid]=$device_obj;
            }
        }
        return $array;
    }
    
    public function getDevice($module_name, $uid) {
        $devices=$this->getModuleDevices($module_name);
        return isset($devices[$uid])?$devices[$uid]:null;
    }

    public function setModuleDevices(string $module_name,&$devices_array) {
        shm_put_var($this->shm,$this->getKeyByModuleName($module_name),$devices_array);
    }

    public function getModuleList() {
        $keys=@shm_get_var($this->shm,1);
        if (!is_array($keys)) {
            return [];
        }
        return array_keys($keys);
    }

    private function getKeyByModuleName($name) {
        $keys=@shm_get_var($this->shm,1);
        if (!is_array($keys)) {
            $keys=[];
        }
        if (!isset($keys[$name])) {
            $keys[$name]=sizeof($keys)+2;
            shm_put_var($this->shm,1,$keys);
        }
        return $keys[$name];
    }

}
