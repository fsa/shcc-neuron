<?php

namespace Yandex\SmartHome\Capabilities;

class ColorTemperature extends Description {

    public $type="devices.capabilities.color_setting";
    public $parameters=['color_model'=>'temperature_k'];
    // TODO: min (2000K), max (9000K), precision
    
}
