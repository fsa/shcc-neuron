<?php

/**
 * Датчики температуры, влажности и давления Xiaomi и Aqara
 */

namespace Xiaomi\Devices;

class XiaomiHTSensor extends AbstractDevice {

    private $temperature;
    private $humidity;

    protected function updateParam($param,$value) {
        switch ($param) {
            case "temperature":
                $this->temperature=$value/100;
                break;
            case "humidity":
                $this->humidity=$value/100;
                break;
            default:
                echo "$param => $value\n";
        }
    }

}
