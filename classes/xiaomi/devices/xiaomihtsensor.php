<?php

/**
 * Датчик температуры и влажности Xiaomi
 */

namespace Xiaomi\Devices;

class XiaomiHTSensor extends AbstractDevice implements \SmartHome\SensorsInterface {

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

    public function getDeviceDescription(): string {
        return "Xiaomi Mi Smart Temperature and Humidity Sensor";
    }

    public function getDeviceStatus(): string {
        $result=[];
        if($this->temperature) {
            $result[]=sprintf('Температура воздуха %+.1f &deg;C.',$this->temperature);
        }
        if($this->humidity) {
            $result[]=sprintf('Относительная влажность %.1f%%.',$this->humidity);
        }
        if($this->voltage) {
            $result[]=sprintf('Батарея CR2032: %.3f В.',$this->voltage);
        }
        return join(' ',$result);
    }

    public function getDeviceIndicators(): array {
        return [];
    }

    public function getDeviceMeters(): array {
        return ['temperature'=>'Температура воздуха, &deg;C','humidity'=>'Относительная влажность, %'];
    }

}
