<?php

namespace Xiaomi\Devices;

class Unknown extends AbstractDevice {

    private $params;
    
    public function __construct() {
        $this->params=new \stdClass();
        parent::__construct();
    }

    protected function updateParam($param,$value) {
        $this->params->$param->$value="action";
    }

    public function getDeviceDescription(): string {
        return "Неизвестный тип устройства";
    }

    public function getDeviceIndicators(): array {
        return [];
    }

    public function getDeviceMeters(): array {
        return [];
    }

    public function getDeviceStatus(): string {
        return $this->model.'=>'.print_r($this->params,true);
    }

}
