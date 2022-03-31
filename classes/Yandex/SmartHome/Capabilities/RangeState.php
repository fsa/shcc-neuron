<?php

namespace Yandex\SmartHome\Capabilities;

class RangeState extends State {

    public $type="devices.capabilities.range";

    public function __construct(string $instance, $value) {
        $this->state=[
            "instance"=>$instance,
            "value"=>$value
        ];
    }
    
    

}
