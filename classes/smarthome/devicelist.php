<?php

namespace SmartHome;

class DeviceList {

    private $list;
    private $fetch_class;

    public function __construct() {
        
    }

    public function getModuleList() {
        $ms=new \SmartHome\DeviceMemoryStorage;
        return $ms->getModuleList();
    }

    public function query($module_name) {
        $ms=new \SmartHome\DeviceMemoryStorage;
        $this->list=$ms->getModuleDevices($module_name);
        $this->fetch_class=\stdClass::class;
    }
    
    public function setFetchClass($class_name) {
        $this->fetch_class=$class_name;
    }

    public function fetch() {
        $device=array_shift($this->list);
        if (is_null($device)) {
            return null;
        }
        $result=new $this->fetch_class;
        $result->obj=$device;
        $result->id=$device->getDeviceId();
        $result->name=$device->getDeviceDescription();
        $result->status=$device->getDeviceStatus();
        $date=$device->getLastUpdate();
        $result->updated=$date==0?'Offline':date('d.m.Y H:i:s',$date);
        return $result;
    }

}
