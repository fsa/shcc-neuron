<?php

namespace Yandex\SmartHome;

class DeviceResult {
    
    public $id;
    public $capabilities;
    
    public function __construct(string $id) {
        $this->id=$id;
    }
    
    public function setId(string $id) {
        $this->id=$id;
    }


    public function addCapability(Capabilities\Result $state) {
        $this->capabilities[]=$state;
    }
}