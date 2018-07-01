<?php

namespace SmartHome\Entity;

class Device {
    public $id;
    public $unique_name;
    public $module_id;
    public $uid;
    public $name;
    public $classname;
    public $init_data;
    public $place_id;
    public $disabled;
    
    public function setInitData($data) {
        $this->init_data=json_encode($data);
    }
    
    public function getInitData() {
        return json_decode($this->init_data,true);
    }
}