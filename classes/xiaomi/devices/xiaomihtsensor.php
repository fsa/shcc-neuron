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
                $this->setTemperature($value);
                break;
            case "humidity":
                $this->setHumidity($value);
                break;
            default:
                echo "$param => $value\n";
        }
    }
    public function setTemperature($value) {
        $last=$this->temperature;
        $this->temperature=$value/100;
        if ($this->temperature!=$last) {
            $this->actions['temperature']=$this->temperature;
        }
    }

    public function setHumidity($value) {
        $last=$this->humidity;
        $this->humidity=$value/100;
        if ($this->humidity!=$last) {
            $this->actions['humidity']=$this->humidity;
        }
    }
}
