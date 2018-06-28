<?php

/**
 * Датчик температуры и влажности Xiaomi
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

    private function setTemperature($value) {
        $last=$this->temperature;
        $this->temperature=$value/100;
        if ($this->temperature!=$last) {
            $this->actions['temperature']=$this->temperature;
        }
    }

    private function setHumidity($value) {
        $last=$this->humidity;
        $this->humidity=$value/100;
        if ($this->humidity!=$last) {
            $this->actions['humidity']=$this->humidity;
        }
    }

    public function getTemperature() {
        return $this->temperature;
    }

    public function getHumidity() {
        return $this->humidity;
    }

    public function getDeviceName() {
        return "Xiaomi Mi Smart Temperature and Humidity Sensor";
    }

    public function __toString() {
        return sprintf('Температура воздуха %+.1f &deg;C, относительная влажность %.1f%%. Батарея CR2032: %.3f В.',$this->temperature,$this->humidity,$this->voltage);
    }

    public function getDeviceIndicators(): array {
        return [];
    }

    public function getDeviceMeters(): array {
        return ['temperature','humidity'];
    }

}
