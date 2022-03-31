<?php

namespace Yandex\SmartHome\Capabilities;

class ColorModel extends Description {

    public $type="devices.capabilities.color_setting";
    public $parameters=[];
    
    public function setTemperatureK(int $min=2000, int $max=9000, int $precision=400): void {
        $this->parameters["temperature_k"]=['min'=>$min, 'max'=>$max, 'precision'=>$precision];
    }
    
    public function setRGBModel() {
        $this->parameters['color_model']='rgb';
    }

    public function setHSVModel() {
        $this->parameters['color_model']='hsv';
    }
    
}
