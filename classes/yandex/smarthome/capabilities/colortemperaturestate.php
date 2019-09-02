<?php

namespace Yandex\SmartHome\Capabilities;

class ColorTemperatureState extends State {

    public $type="devices.capabilities.color_setting";

    public function __construct(int $value) {
        $this->state=[
            "instance"=>'temperature_k',
            "value"=>$value
        ];
    }

}
