<?php

/**
 * Датчики температуры, влажности и давления Xiaomi и Aqara
 */

namespace Xiaomi\Devices;

class AqaraWeatherSensor extends AbstractDevice {

    private $temperature;
    private $humidity;
    private $pressureKPa;
    private $pressure;

    protected function updateParam($param,$value) {
        switch ($param) {
            case "temperature":
                $this->setTemperature($value);
                break;
            case "humidity":
                $this->setHumidity($value);
                break;
            case "pressure":
                $this->setPressure($value);
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

    public function setPressure($value) {
        $last=$this->pressureKPa;
        $this->pressureKPa=$value;
        $this->pressure=round($value*760/101325,2);
        if ($this->pressureKPa!=$last) {
            $this->actions['pressure']=$this->pressure;
        }
    }

}
