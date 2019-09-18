<?php

namespace Yandex\SmartHome;

class DeviceState {
    
    public $id;
    public $capabilities;
    
    public function __construct(string $id) {
        $this->id=$id;
    }
    
    public function setId(string $id) {
        $this->id=$id;
    }


    public function addCapability(Capabilities\State $state) {
        $this->capabilities[]=$state;
    }
}