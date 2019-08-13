<?php

namespace Yandex\SmartHome\Capabilities;

class onOffState extends State {

    public $type="devices.capabilities.on_off";
    
    public function __construct($instance, $value) {
        $this->state=[
            "instance"=>$instance,
            "value"=>$value
        ];
    }
    
}
