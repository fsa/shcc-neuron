<?php

namespace Yandex\SmartHome\Capabilities;

class Range extends Description {

    public $type="devices.capabilities.range";
    public $parameters=[];
    
    public function __construct(string $instance, ?bool $state=true) {
        $this->parameters['instance']=$instance;
        parent::__construct($state);
    }
    
    public function setUnit(string $unit) {
        $this->parameters['unit']='unit.'.$unit;
    }
    
    public function setRange(float $min, float $max, float $precision=1) {
        $this->parameters['range']=['min'=>$min, 'max'=>$max, 'precision'=>$precision];
    }
    
}
