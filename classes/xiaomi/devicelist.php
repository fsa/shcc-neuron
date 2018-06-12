<?php

namespace Xiaomi;

class DeviceList {
    private $list;
    
    public function __construct() {
        $this->query();
    }
    
    public function query() {
        $ms=new \MemoryStorage;
        $this->list=$ms->getArray('xiaomi');        
    }

    public function fetch() {
        $device=array_shift($this->list);
        if(is_null($device)) {
            return null;
        }
        $result=new \stdClass();
        $result->sid=$device->getDeviceId();
        $result->name=$device->getDeviceName();
        $result->status_description=$device;
        $result->voltage=$device->getVoltage();
        $result->updated=date('Y-m-d H:i:sP',$device->getLastUpdate());
        return $result;
    }
    
    public function closeCursor() {
        
    }
}