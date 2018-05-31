<?php

/**
 * Датчики температуры, влажности и давления Xiaomi и Aqara
 */

namespace Xiaomi\Devices;

class TemperatureHumiditySensor extends AbstractDevice {

    private $temperature;
    private $humidity;
    private $pressureKPa;
    private $pressure;

    protected function updateParam($param,$value) {
        switch ($param) {
            case "temperature":
                $this->temperature=$value/100;
                break;
            case "humidity":
                $this->humidity=$value/100;
                break;
            case "pressure":
                $this->pressureKPa=$value;
                $this->pressure=round($value*760/101325,2);
                break;
            default:
                echo "$param => $value\n";
        }
    }

}
