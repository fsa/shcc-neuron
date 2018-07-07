<?php

namespace Xiaomi\Devices;

class Unknown extends AbstractDevice {

    private $params=[];

    protected function updateParam($param,$value) {
        $this->params[$param]=$value;
    }

    public function getDeviceDescription() {
        return "Неизвестный тип устройства";
    }

    public function getDeviceIndicators(): array {
        return [];
    }

    public function getDeviceMeters(): array {
        return [];
    }

    public function getDeviceStatus() {
        return $this->model.'=>'.print_r($this->params,true);
    }

}
