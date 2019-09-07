<?php

namespace SmartHome;

class DeviceMemoryStorage {

    const PROJ='a';
    const CHMOD=0600;
    const MEMSIZE=1048576;

    private $shm;

    public function __construct() {
        $memsize=\Settings::get('device_memory_storage', self::MEMSIZE);
        $file=ftok(__FILE__,self::PROJ);
        $this->shm=shm_attach($file,$memsize,self::CHMOD);
        if (shm_has_var($this->shm,1)) {
            return;
        }
        $modules=[];
        if(!shm_put_var($this->shm,1,$modules)) {
            throw new \AppException('Не удалось инициализировать разделяемую память. Дальнейшая работа с устройствами невозможна.');
        }
    }

    public function getModuleDevices(string $module_name) {
        $key=$this->getKeyByModuleName($module_name);
        return shm_get_var($this->shm,$key);
    }
    
    public function getDevice($module_name, $uid) {
        $devices=$this->getModuleDevices($module_name);
        return isset($devices[$uid])?$devices[$uid]:null;
    }

    public function setModuleDevices(string $module_name,&$devices_array) {
        shm_put_var($this->shm,$this->getKeyByModuleName($module_name),$devices_array);
    }

    public function getModuleList() {
        $keys=shm_get_var($this->shm,1);
        return array_keys($keys);
    }

    private function getKeyByModuleName($name) {
        $keys=shm_get_var($this->shm,1);
        if (!isset($keys[$name])) {
            $keys[$name]=sizeof($keys)+2;
            shm_put_var($this->shm,1,$keys);
            $devices=Devices::getDevicesByModuleName($name);
            shm_put_var($this->shm, $keys[$name], $devices);
        }
        return $keys[$name];
    }

}
