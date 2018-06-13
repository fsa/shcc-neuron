<?php

namespace Yeelight;

class DeviceList {

    private $list;

    public function __construct() {
        $this->query();
    }

    public function query() {
        $ms=new \MemoryStorage;
        $this->list=$ms->getArray('yeelight');
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
