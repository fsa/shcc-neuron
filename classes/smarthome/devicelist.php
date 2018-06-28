<?php

namespace SmartHome;

class DeviceList {

    private $list;

    public function __construct() {
        
    }

    public function getModuleList() {
        $ms=new \SmartHome\DeviceMemoryStorage;
        return $ms->getModuleList();
    }

    public function query($module_name) {
        $ms=new \SmartHome\DeviceMemoryStorage;
        $this->list=$ms->getModuleDevices($module_name);
    }

    public function fetch() {
        $device=array_shift($this->list);
        if (is_null($device)) {
            return null;
        }
        $result=new \stdClass();
        $result->id=$device->getDeviceId();
        $result->name=$device->getDeviceName();
        $result->status_description=$device;
        $result->updated=date('d.m.Y H:i:s',$device->getLastUpdate());
        return $result;
    }

}
