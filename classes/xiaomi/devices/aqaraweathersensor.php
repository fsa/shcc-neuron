<?php

/**
 * SHCC 0.7.0-dev
 * 2020-11-25
 * Датчик температуры, влажности и давления Aqara
 */

namespace Xiaomi\Devices;

class AqaraWeatherSensor extends AbstractDevice {

    private $temperature;
    private $humidity;
    private $pressureKPa;
    private $pressure;

    protected function updateParam($param, $value) {
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
                $this->showUnknownParam($param, $value);
        }
    }

    private function setTemperature($value) {
        if ($value<-5000) {
            # Aqara bug: Temperature -100 deg.C.
            return;
        }
        $last=$this->temperature;
        $this->temperature=$value/100;
        if ($this->temperature!=$last) {
            $this->events['temperature']=$this->temperature;
        }
    }

    private function setHumidity($value) {
        if ($value<0 or $value>10000) {
            # Aqara bug: Humidity 654.36%
            return;
        }
        $last=$this->humidity;
        $this->humidity=$value/100;
        if ($this->humidity!=$last) {
            $this->events['humidity']=$this->humidity;
        }
    }

    private function setPressure($value) {
        $last=$this->pressureKPa;
        $this->pressureKPa=$value;
        $this->pressure=round($value*760/101325, 2);
        if ($this->pressureKPa!=$last) {
            $this->events['pressure']=$this->pressure;
        }
    }

    public function getTemperature() {
        return $this->temperature;
    }

    public function getHumidity() {
        return $this->humidity;
    }

    public function getPressure() {
        return $this->pressure;
    }

    public function getDescription(): string {
        return "Aqara Temperature Humidity Sensor";
    }

    public function getState(): array {
        $state=[];
        if (!is_null($this->temperature)) {
            $state['temperature']=round($this->temperature, 1);
        }
        if (!is_null($this->humidity)) {
            $state['humidity']=round($this->humidity);
        }
        if (!is_null($this->pressure)) {
            $state['pressure']=round($this->pressure);
        }
        if (!is_null($this->voltage)) {
            $state['voltage']=$this->voltage;
        }
        return $state;
    }

    public function __toString(): string {
        $result=[];
        if ($this->temperature) {
            $result[]=sprintf('Температура воздуха %+.1f &deg;C.', $this->temperature);
        }
        if ($this->humidity) {
            $result[]=sprintf('Относительная влажность %.1f%%.', $this->humidity);
        }
        if ($this->pressure) {
            $result[]=sprintf('Атмосферное давление %.1f мм.рт.ст.', $this->pressure);
        }
        if ($this->voltage) {
            $result[]=sprintf('Батарея CR2032: %.3f В.', $this->voltage);
        }
        return join(' ', $result);
    }

    public function getEventsList(): array {
        return [
            'temperature',
            'humidity',
            'pressure',
            'voltage'
        ];
    }

}
