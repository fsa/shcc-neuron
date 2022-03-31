<?php

namespace Yandex\SmartHome\Capabilities;

class OnOffState extends State {

    public $type="devices.capabilities.on_off";
    
    public function __construct(bool $value) {
        $this->state=[
            "instance"=>'on',
            "value"=>$value
        ];
    }
    
}
