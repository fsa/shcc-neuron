<?php

/**
 * Датчик температуры и влажности Xiaomi
 */

namespace FSA\Xiaomi\Devices;

class XiaomiHTSensor extends AbstractDevice
{
    private $temperature;
    private $humidity;

    protected function updateParam($param, $value)
    {
        switch ($param) {
            case "temperature":
                $this->setTemperature($value);
                break;
            case "humidity":
                $this->setHumidity($value);
                break;
            default:
                $this->showUnknownParam($param, $value);
        }
    }

    private function setTemperature($value)
    {
        $last = $this->temperature;
        $this->temperature = $value / 100;
        if ($this->temperature != $last) {
            $this->events['temperature'] = $this->temperature;
        }
    }

    private function setHumidity($value)
    {
        $last = $this->humidity;
        $this->humidity = $value / 100;
        if ($this->humidity != $last) {
            $this->events['humidity'] = $this->humidity;
        }
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function getHumidity()
    {
        return $this->humidity;
    }

    public function getDescription(): string
    {
        return "Xiaomi Mi Smart Temperature and Humidity Sensor";
    }

    public function getState(): array
    {
        $state = [];
        if (!is_null($this->temperature)) {
            $state['temperature'] = round($this->temperature, 1);
        }
        if (!is_null($this->humidity)) {
            $state['humidity'] = round($this->humidity);
        }
        if (!is_null($this->voltage)) {
            $state['voltage'] = $this->voltage;
        }
        return $state;
    }

    public function __toString(): string
    {
        $result = [];
        if ($this->temperature) {
            $result[] = sprintf('Температура воздуха %+.1f &deg;C.', $this->temperature);
        }
        if ($this->humidity) {
            $result[] = sprintf('Относительная влажность %.1f%%.', $this->humidity);
        }
        if ($this->voltage) {
            $result[] = sprintf('Батарея CR2032: %.3f В.', $this->voltage);
        }
        return join(' ', $result);
    }

    public function getEventsList(): array
    {
        return ['temperature', 'humidity', 'voltage'];
    }
}
